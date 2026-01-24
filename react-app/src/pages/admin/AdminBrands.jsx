import React, { useEffect, useMemo, useState } from 'react';
import { adminFetch } from '../../lib/adminFetch';
import { copyTable, downloadTextFile, printTable, toCsv } from '../../lib/tableTools';

export default function AdminBrands() {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [newName, setNewName] = useState('');
  const [editingId, setEditingId] = useState(null);
  const [editingName, setEditingName] = useState('');
  const [search, setSearch] = useState('');

  const sorted = useMemo(() => rows.slice().sort((a, b) => (b.brand_id || 0) - (a.brand_id || 0)), [rows]);
  const filtered = useMemo(() => {
    const q = search.trim().toLowerCase();
    if (!q) return sorted;
    return sorted.filter((r) => {
      const hay = `${r.brand_id ?? ''} ${r.brand_name ?? ''}`.toLowerCase();
      return hay.includes(q);
    });
  }, [sorted, search]);

  const columns = useMemo(
    () => [
      { label: 'ID', get: (r) => r.brand_id },
      { label: 'Name', get: (r) => r.brand_name || '' },
    ],
    [],
  );

  async function load() {
    setLoading(true);
    setError('');
    try {
      const payload = await adminFetch('/admin/brands');
      setRows(payload?.data || []);
    } catch (e) {
      setError(e.message || 'Failed to load brands');
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    load();
  }, []);

  async function createBrand(e) {
    e.preventDefault();
    const brand_name = newName.trim();
    if (!brand_name) return;
    setError('');
    try {
      await adminFetch('/admin/brands', { method: 'POST', body: JSON.stringify({ brand_name }) });
      setNewName('');
      await load();
    } catch (e2) {
      setError(e2.message || 'Failed to create brand');
    }
  }

  function startEdit(row) {
    setEditingId(row.brand_id);
    setEditingName(row.brand_name || '');
  }

  function cancelEdit() {
    setEditingId(null);
    setEditingName('');
  }

  async function saveEdit(id) {
    const brand_name = editingName.trim();
    if (!brand_name) return;
    setError('');
    try {
      await adminFetch(`/admin/brands/${id}`, { method: 'POST', body: JSON.stringify({ brand_name }) });
      cancelEdit();
      await load();
    } catch (e) {
      setError(e.message || 'Failed to update brand');
    }
  }

  async function deleteBrand(id) {
    if (!confirm('Delete this brand?')) return;
    setError('');
    try {
      await adminFetch(`/admin/brands/${id}`, { method: 'DELETE' });
      await load();
    } catch (e) {
      setError(e.message || 'Failed to delete brand');
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
    downloadTextFile(`brands-${new Date().toISOString().slice(0, 10)}.csv`, csv, 'text/csv;charset=utf-8');
  }

  function onPrint() {
    printTable('Brands', filtered, columns);
  }

  return (
    <div>
      <div style={{ display: 'flex', gap: 10, alignItems: 'center', justifyContent: 'space-between' }}>
        <h2 style={{ marginTop: 0, color: '#000000' }}>Brands</h2>
        <div style={{ display: 'flex', gap: 8, flexWrap: 'wrap' }}>
          <button
            type="button"
            onClick={onCopy}
            style={{ padding: '8px 10px', borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#f5f5f5', color: '#000000', cursor: 'pointer' }}
          >
            Copy
          </button>
          <button
            type="button"
            onClick={onCsv}
            style={{ padding: '8px 10px', borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#f5f5f5', color: '#000000', cursor: 'pointer' }}
          >
            CSV
          </button>
          <button
            type="button"
            onClick={onPrint}
            style={{ padding: '8px 10px', borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#f5f5f5', color: '#000000', cursor: 'pointer' }}
          >
            Print
          </button>
        </div>
      </div>

      <form onSubmit={createBrand} style={{ display: 'flex', gap: 8, alignItems: 'center', marginBottom: 12 }}>
        <input
          value={newName}
          onChange={(e) => setNewName(e.target.value)}
          placeholder="New brand name"
          style={{ flex: 1, padding: 10, borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#ffffff', color: '#000000' }}
        />
        <button
          type="submit"
          style={{ padding: '10px 12px', borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: 'rgba(90,103,216,0.8)', color: '#ffffff', cursor: 'pointer' }}
        >
          Add
        </button>
        <button
          type="button"
          onClick={load}
          style={{ padding: '10px 12px', borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#f5f5f5', color: '#000000', cursor: 'pointer' }}
        >
          Refresh
        </button>
        <input
          value={search}
          onChange={(e) => setSearch(e.target.value)}
          placeholder="Search…"
          style={{ flex: 1, padding: 10, borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#ffffff', color: '#000000' }}
        />
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
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Name</th>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Actions</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((row) => (
                <tr key={row.brand_id}>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)', color: '#000000' }}>{row.brand_id}</td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)', color: '#000000' }}>
                    {editingId === row.brand_id ? (
                      <input
                        value={editingName}
                        onChange={(e) => setEditingName(e.target.value)}
                        style={{ width: '100%', padding: 10, borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#ffffff', color: '#000000' }}
                      />
                    ) : (
                      <span style={{ color: '#000000' }}>{row.brand_name}</span>
                    )}
                  </td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)' }}>
                    {editingId === row.brand_id ? (
                      <div style={{ display: 'flex', gap: 8 }}>
                        <button
                          onClick={() => saveEdit(row.brand_id)}
                          style={{ padding: '8px 10px', borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: 'rgba(90,103,216,0.8)', color: '#ffffff', cursor: 'pointer' }}
                        >
                          Save
                        </button>
                        <button
                          onClick={cancelEdit}
                          style={{ padding: '8px 10px', borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#f5f5f5', color: '#000000', cursor: 'pointer' }}
                        >
                          Cancel
                        </button>
                      </div>
                    ) : (
                      <div style={{ display: 'flex', gap: 8 }}>
                        <button
                          onClick={() => startEdit(row)}
                          style={{ padding: '8px 10px', borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#f5f5f5', color: '#000000', cursor: 'pointer' }}
                        >
                          Edit
                        </button>
                        <button
                          onClick={() => deleteBrand(row.brand_id)}
                          style={{ padding: '8px 10px', borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: 'rgba(255,80,80,0.3)', color: '#000000', cursor: 'pointer' }}
                        >
                          Delete
                        </button>
                      </div>
                    )}
                  </td>
                </tr>
              ))}
              {filtered.length === 0 ? (
                <tr>
                  <td colSpan={3} style={{ padding: 12, color: '#000000' }}>
                    No brands found.
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

