import React, { useEffect, useMemo, useState } from 'react';
import { adminFetch } from '../../lib/adminFetch';
import { copyTable, downloadTextFile, printTable, toCsv } from '../../lib/tableTools';

const inputStyle = {
  padding: 10,
  borderRadius: 10,
  border: '1px solid rgba(0,0,0,0.2)',
  background: '#ffffff',
  color: '#000000',
};

const buttonStyle = {
  padding: '10px 12px',
  borderRadius: 10,
  border: '1px solid rgba(0,0,0,0.2)',
  background: '#f5f5f5',
  color: '#000000',
  cursor: 'pointer',
};

export default function AdminCategories() {
  const [rows, setRows] = useState([]);
  const [brands, setBrands] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [search, setSearch] = useState('');

  const [newBrandId, setNewBrandId] = useState('');
  const [newName, setNewName] = useState('');

  const [editingId, setEditingId] = useState(null);
  const [editingBrandId, setEditingBrandId] = useState('');
  const [editingName, setEditingName] = useState('');

  const sorted = useMemo(() => rows.slice().sort((a, b) => (b.cat_id || 0) - (a.cat_id || 0)), [rows]);
  const filtered = useMemo(() => {
    const q = search.trim().toLowerCase();
    if (!q) return sorted;
    return sorted.filter((r) => {
      const name = r.caterogyName ?? r.category_name ?? '';
      const hay = `${r.cat_id ?? ''} ${r.brand_name ?? ''} ${name}`.toLowerCase();
      return hay.includes(q);
    });
  }, [sorted, search]);

  const columns = useMemo(
    () => [
      { label: 'ID', get: (r) => r.cat_id },
      { label: 'Brand', get: (r) => r.brand_name || '' },
      { label: 'Category', get: (r) => r.caterogyName ?? r.category_name ?? '' },
    ],
    [],
  );

  async function load() {
    setLoading(true);
    setError('');
    try {
      const [cats, br] = await Promise.all([adminFetch('/admin/categories'), adminFetch('/admin/brands')]);
      setRows(cats?.data || []);
      setBrands(br?.data || []);
      if (!newBrandId && (br?.data || []).length > 0) setNewBrandId(String((br.data[0] || {}).brand_id || ''));
    } catch (e) {
      setError(e.message || 'Failed to load categories');
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  async function createCategory(e) {
    e.preventDefault();
    const brand_id = Number(newBrandId);
    const category_name = newName.trim();
    if (!brand_id || !category_name) return;
    setError('');
    try {
      await adminFetch('/admin/categories', { method: 'POST', body: JSON.stringify({ brand_id, category_name }) });
      setNewName('');
      await load();
    } catch (e2) {
      setError(e2.message || 'Failed to create category');
    }
  }

  function startEdit(row) {
    setEditingId(row.cat_id);
    setEditingBrandId(String(row.brandId ?? row.brand_id ?? ''));
    setEditingName(row.caterogyName ?? row.category_name ?? '');
  }

  function cancelEdit() {
    setEditingId(null);
    setEditingBrandId('');
    setEditingName('');
  }

  async function saveEdit(id) {
    const brand_id = Number(editingBrandId);
    const category_name = editingName.trim();
    if (!brand_id || !category_name) return;
    setError('');
    try {
      await adminFetch(`/admin/categories/${id}`, { method: 'POST', body: JSON.stringify({ brand_id, category_name }) });
      cancelEdit();
      await load();
    } catch (e) {
      setError(e.message || 'Failed to update category');
    }
  }

  async function deleteCategory(id) {
    if (!confirm('Delete this category?')) return;
    setError('');
    try {
      await adminFetch(`/admin/categories/${id}`, { method: 'DELETE' });
      await load();
    } catch (e) {
      setError(e.message || 'Failed to delete category');
    }
  }

  async function onCopy() {
    try {
      await copyTable(filtered, columns);
      alert('Copied (tab-separated) to clipboard.');
    } catch (e) {
      alert(e.message || 'Copy failed');
    }
  }

  function onCsv() {
    const csv = toCsv(filtered, columns);
    downloadTextFile(`categories-${new Date().toISOString().slice(0, 10)}.csv`, csv, 'text/csv;charset=utf-8');
  }

  function onPrint() {
    printTable('Categories', filtered, columns);
  }

  return (
    <div>
      <div style={{ display: 'flex', gap: 10, alignItems: 'center', justifyContent: 'space-between' }}>
        <h2 style={{ marginTop: 0, color: '#000000' }}>Categories</h2>
        <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
          <button type="button" onClick={onCopy} style={{ ...buttonStyle, padding: '8px 10px' }}>
            Copy
          </button>
          <button type="button" onClick={onCsv} style={{ ...buttonStyle, padding: '8px 10px' }}>
            CSV
          </button>
          <button type="button" onClick={onPrint} style={{ ...buttonStyle, padding: '8px 10px' }}>
            Print
          </button>
        </div>
      </div>

      <form onSubmit={createCategory} style={{ display: 'flex', gap: 8, alignItems: 'center', marginBottom: 12, flexWrap: 'wrap' }}>
        <select value={newBrandId} onChange={(e) => setNewBrandId(e.target.value)} style={{ ...inputStyle, minWidth: 220 }}>
          {(brands || []).map((b) => (
            <option key={b.brand_id} value={String(b.brand_id)}>
              {b.brand_name}
            </option>
          ))}
        </select>
        <input value={newName} onChange={(e) => setNewName(e.target.value)} placeholder="New category name" style={{ ...inputStyle, flex: 1, minWidth: 260 }} />
        <button type="submit" style={{ ...buttonStyle, background: 'rgba(90,103,216,0.8)', color: '#ffffff' }}>
          Add
        </button>
        <button type="button" onClick={load} style={buttonStyle}>
          Refresh
        </button>
        <input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Search…" style={{ ...inputStyle, flex: 1, minWidth: 240 }} />
      </form>

      {error ? <div style={{ color: '#d32f2f', marginBottom: 10 }}>{error}</div> : null}
      {loading ? (
        <div style={{ color: '#000000' }}>Loading…</div>
      ) : (
        <div style={{ overflowX: 'auto' }}>
          <table style={{ width: '100%', borderCollapse: 'collapse' }}>
            <thead>
              <tr>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>ID</th>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Brand</th>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Category</th>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Actions</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((row) => (
                <tr key={row.cat_id}>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)', color: '#000000' }}>{row.cat_id}</td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)' }}>
                    {editingId === row.cat_id ? (
                      <select value={editingBrandId} onChange={(e) => setEditingBrandId(e.target.value)} style={{ ...inputStyle, width: '100%' }}>
                        {(brands || []).map((b) => (
                          <option key={b.brand_id} value={String(b.brand_id)}>
                            {b.brand_name}
                          </option>
                        ))}
                      </select>
                    ) : (
                      <span style={{ color: '#000000' }}>{row.brand_name}</span>
                    )}
                  </td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)' }}>
                    {editingId === row.cat_id ? (
                      <input value={editingName} onChange={(e) => setEditingName(e.target.value)} style={{ ...inputStyle, width: '100%' }} />
                    ) : (
                      <span style={{ color: '#000000' }}>{row.caterogyName ?? row.category_name ?? ''}</span>
                    )}
                  </td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)' }}>
                    {editingId === row.cat_id ? (
                      <div style={{ display: 'flex', gap: 8 }}>
                        <button type="button" onClick={() => saveEdit(row.cat_id)} style={{ ...buttonStyle, padding: '8px 10px', background: 'rgba(90,103,216,0.8)', color: '#ffffff' }}>
                          Save
                        </button>
                        <button type="button" onClick={cancelEdit} style={{ ...buttonStyle, padding: '8px 10px' }}>
                          Cancel
                        </button>
                      </div>
                    ) : (
                      <div style={{ display: 'flex', gap: 8 }}>
                        <button type="button" onClick={() => startEdit(row)} style={{ ...buttonStyle, padding: '8px 10px' }}>
                          Edit
                        </button>
                        <button type="button" onClick={() => deleteCategory(row.cat_id)} style={{ ...buttonStyle, padding: '8px 10px', background: 'rgba(255,80,80,0.3)', color: '#000000' }}>
                          Delete
                        </button>
                      </div>
                    )}
                  </td>
                </tr>
              ))}
              {filtered.length === 0 ? (
                <tr>
                  <td colSpan={4} style={{ padding: 12, color: '#000000' }}>
                    No categories found.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

