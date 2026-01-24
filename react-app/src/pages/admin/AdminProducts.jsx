import React, { useEffect, useMemo, useRef, useState } from 'react';
import { adminFetch } from '../../lib/adminFetch';
import { backendPath } from '../../lib/backend';
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

function categoryLabel(c) {
  return c?.caterogyName ?? c?.category_name ?? '';
}

export default function AdminProducts() {
  const [rows, setRows] = useState([]);
  const [brands, setBrands] = useState([]);
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');
  const [search, setSearch] = useState('');

  const [newBrandId, setNewBrandId] = useState('');
  const [newCatId, setNewCatId] = useState('');
  const [newName, setNewName] = useState('');
  const [newDesc, setNewDesc] = useState('');
  const [newTech, setNewTech] = useState('');
  const [newImageFile, setNewImageFile] = useState(null);
  const newImageInputRef = useRef(null);

  const [editingId, setEditingId] = useState(null);
  const [editingBrandId, setEditingBrandId] = useState('');
  const [editingCatId, setEditingCatId] = useState('');
  const [editingName, setEditingName] = useState('');
  const [editingDesc, setEditingDesc] = useState('');
  const [editingTech, setEditingTech] = useState('');
  const [editingImageFile, setEditingImageFile] = useState(null);
  const editingImageInputRef = useRef(null);

  const sorted = useMemo(() => rows.slice().sort((a, b) => (b.pro_id || 0) - (a.pro_id || 0)), [rows]);
  const filtered = useMemo(() => {
    const q = search.trim().toLowerCase();
    if (!q) return sorted;
    return sorted.filter((r) => {
      const hay = `${r.pro_id ?? ''} ${r.pro_name ?? ''} ${r.brand_name ?? ''} ${r.caterogyName ?? ''} ${r.pro_desc ?? ''} ${r.pro_tech ?? ''}`.toLowerCase();
      return hay.includes(q);
    });
  }, [sorted, search]);

  const columns = useMemo(
    () => [
      { label: 'ID', get: (r) => r.pro_id },
      { label: 'Brand', get: (r) => r.brand_name || '' },
      { label: 'Category', get: (r) => r.caterogyName ?? r.category_name ?? '' },
      { label: 'Name', get: (r) => r.pro_name || '' },
      { label: 'Tech', get: (r) => r.pro_tech || '' },
    ],
    [],
  );

  const catsForNewBrand = useMemo(() => {
    const bid = Number(newBrandId);
    if (!bid) return [];
    return (categories || []).filter((c) => Number(c.brandId ?? c.brand_id) === bid);
  }, [categories, newBrandId]);

  const catsForEditingBrand = useMemo(() => {
    const bid = Number(editingBrandId);
    if (!bid) return [];
    return (categories || []).filter((c) => Number(c.brandId ?? c.brand_id) === bid);
  }, [categories, editingBrandId]);

  async function load() {
    setLoading(true);
    setError('');
    try {
      const [prod, br, cats] = await Promise.all([adminFetch('/admin/products'), adminFetch('/admin/brands'), adminFetch('/admin/categories')]);
      setRows(prod?.data || []);
      setBrands(br?.data || []);
      setCategories(cats?.data || []);

      if (!newBrandId && (br?.data || []).length > 0) {
        const firstBrandId = String((br.data[0] || {}).brand_id || '');
        setNewBrandId(firstBrandId);
      }
    } catch (e) {
      setError(e.message || 'Failed to load products');
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  useEffect(() => {
    if (!newBrandId) return;
    if (catsForNewBrand.length === 0) {
      setNewCatId('');
      return;
    }
    if (!newCatId || !catsForNewBrand.some((c) => String(c.cat_id) === String(newCatId))) {
      setNewCatId(String(catsForNewBrand[0].cat_id));
    }
  }, [newBrandId, catsForNewBrand, newCatId]);

  useEffect(() => {
    if (!editingId) return;
    if (!editingBrandId) return;
    if (catsForEditingBrand.length === 0) {
      setEditingCatId('');
      return;
    }
    if (!editingCatId || !catsForEditingBrand.some((c) => String(c.cat_id) === String(editingCatId))) {
      setEditingCatId(String(catsForEditingBrand[0].cat_id));
    }
  }, [editingId, editingBrandId, catsForEditingBrand, editingCatId]);

  async function createProduct(e) {
    e.preventDefault();
    const brand_id = Number(newBrandId);
    const cat_id = Number(newCatId);
    const pro_name = newName.trim();
    if (!brand_id || !cat_id || !pro_name) return;
    setError('');
    try {
      const form = new FormData();
      form.append('brand_id', String(brand_id));
      form.append('cat_id', String(cat_id));
      form.append('pro_name', pro_name);
      form.append('pro_desc', newDesc);
      form.append('pro_tech', newTech);
      if (newImageFile) form.append('product_img', newImageFile);
      await adminFetch('/admin/products', {
        method: 'POST',
        body: form,
      });
      setNewName('');
      setNewDesc('');
      setNewTech('');
      setNewImageFile(null);
      if (newImageInputRef.current) newImageInputRef.current.value = '';
      await load();
    } catch (e2) {
      setError(e2.message || 'Failed to create product');
    }
  }

  function startEdit(row) {
    setEditingId(row.pro_id);
    setEditingBrandId(String(row.brand_id ?? row.brandId ?? ''));
    setEditingCatId(String(row.catId ?? row.cat_id ?? ''));
    setEditingName(row.pro_name || '');
    setEditingDesc(row.pro_desc || '');
    setEditingTech(row.pro_tech || '');
    setEditingImageFile(null);
    if (editingImageInputRef.current) editingImageInputRef.current.value = '';
  }

  function cancelEdit() {
    setEditingId(null);
    setEditingBrandId('');
    setEditingCatId('');
    setEditingName('');
    setEditingDesc('');
    setEditingTech('');
    setEditingImageFile(null);
    if (editingImageInputRef.current) editingImageInputRef.current.value = '';
  }

  async function saveEdit(id) {
    const brand_id = Number(editingBrandId);
    const cat_id = Number(editingCatId);
    const pro_name = editingName.trim();
    if (!brand_id || !cat_id || !pro_name) return;
    setError('');
    try {
      const form = new FormData();
      form.append('brand_id', String(brand_id));
      form.append('cat_id', String(cat_id));
      form.append('pro_name', pro_name);
      form.append('pro_desc', editingDesc);
      form.append('pro_tech', editingTech);
      if (editingImageFile) form.append('product_img', editingImageFile);
      await adminFetch(`/admin/products/${id}`, {
        method: 'POST',
        body: form,
      });
      cancelEdit();
      await load();
    } catch (e) {
      setError(e.message || 'Failed to update product');
    }
  }

  async function deleteProduct(id) {
    if (!confirm('Delete this product?')) return;
    setError('');
    try {
      await adminFetch(`/admin/products/${id}`, { method: 'DELETE' });
      await load();
    } catch (e) {
      setError(e.message || 'Failed to delete product');
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
    downloadTextFile(`products-${new Date().toISOString().slice(0, 10)}.csv`, csv, 'text/csv;charset=utf-8');
  }

  function onPrint() {
    printTable('Products', filtered, columns);
  }

  return (
    <div>
      <div style={{ display: 'flex', gap: 10, alignItems: 'center', justifyContent: 'space-between' }}>
        <h2 style={{ marginTop: 0, color: '#000000' }}>Products</h2>
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

      <form onSubmit={createProduct} style={{ display: 'flex', gap: 8, alignItems: 'center', marginBottom: 12, flexWrap: 'wrap' }}>
        <select value={newBrandId} onChange={(e) => setNewBrandId(e.target.value)} style={{ ...inputStyle, minWidth: 220 }}>
          {(brands || []).map((b) => (
            <option key={b.brand_id} value={String(b.brand_id)}>
              {b.brand_name}
            </option>
          ))}
        </select>
        <select value={newCatId} onChange={(e) => setNewCatId(e.target.value)} style={{ ...inputStyle, minWidth: 220 }} disabled={!newBrandId || catsForNewBrand.length === 0}>
          {catsForNewBrand.length === 0 ? (
            <option value="">No categories</option>
          ) : (
            catsForNewBrand.map((c) => (
              <option key={c.cat_id} value={String(c.cat_id)}>
                {categoryLabel(c)}
              </option>
            ))
          )}
        </select>
        <input value={newName} onChange={(e) => setNewName(e.target.value)} placeholder="Product name" style={{ ...inputStyle, flex: 1, minWidth: 260 }} />
        <input value={newTech} onChange={(e) => setNewTech(e.target.value)} placeholder="Tech (optional)" style={{ ...inputStyle, flex: 1, minWidth: 240 }} />
        <input value={newDesc} onChange={(e) => setNewDesc(e.target.value)} placeholder="Description (optional)" style={{ ...inputStyle, flex: 2, minWidth: 320 }} />
        <label style={{ display: 'flex', alignItems: 'center', gap: 6, cursor: 'pointer', flexWrap: 'wrap' }}>
          <span style={{ fontSize: 13, color: '#000000' }}>Image:</span>
          <input
            ref={newImageInputRef}
            type="file"
            accept="image/*"
            onChange={(e) => setNewImageFile(e.target.files?.[0] ?? null)}
            style={{ ...inputStyle, padding: 6, maxWidth: 200 }}
          />
          {newImageFile ? <span style={{ fontSize: 12, color: '#333' }}>{newImageFile.name}</span> : null}
        </label>
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
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Name</th>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Tech</th>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Image</th>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Actions</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map((row) => (
                <tr key={row.pro_id}>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)', color: '#000000' }}>{row.pro_id}</td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)' }}>
                    {editingId === row.pro_id ? (
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
                    {editingId === row.pro_id ? (
                      <select value={editingCatId} onChange={(e) => setEditingCatId(e.target.value)} style={{ ...inputStyle, width: '100%' }} disabled={!editingBrandId || catsForEditingBrand.length === 0}>
                        {catsForEditingBrand.length === 0 ? (
                          <option value="">No categories</option>
                        ) : (
                          catsForEditingBrand.map((c) => (
                            <option key={c.cat_id} value={String(c.cat_id)}>
                              {categoryLabel(c)}
                            </option>
                          ))
                        )}
                      </select>
                    ) : (
                      <span style={{ color: '#000000' }}>{row.caterogyName ?? row.category_name ?? ''}</span>
                    )}
                  </td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)' }}>
                    {editingId === row.pro_id ? (
                      <input value={editingName} onChange={(e) => setEditingName(e.target.value)} style={{ ...inputStyle, width: '100%' }} />
                    ) : (
                      <span style={{ color: '#000000' }}>{row.pro_name}</span>
                    )}
                  </td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)' }}>
                    {editingId === row.pro_id ? (
                      <input value={editingTech} onChange={(e) => setEditingTech(e.target.value)} style={{ ...inputStyle, width: '100%' }} />
                    ) : (
                      <span style={{ color: '#000000' }}>{row.pro_tech || ''}</span>
                    )}
                  </td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)' }}>
                    {editingId === row.pro_id ? (
                      <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                        {row.pro_img ? (
                          <img src={backendPath(`/uploads/Product/${row.pro_img}`)} alt="" style={{ width: 48, height: 48, objectFit: 'contain', border: '1px solid #ddd', borderRadius: 6 }} />
                        ) : null}
                        <label style={{ display: 'flex', alignItems: 'center', gap: 6, cursor: 'pointer' }}>
                          <input
                            ref={editingImageInputRef}
                            type="file"
                            accept="image/*"
                            onChange={(e) => setEditingImageFile(e.target.files?.[0] ?? null)}
                            style={{ ...inputStyle, padding: 6, width: '100%' }}
                          />
                          {editingImageFile ? <span style={{ fontSize: 12 }}>{editingImageFile.name}</span> : <span style={{ fontSize: 12, opacity: 0.7 }}>Change image (optional)</span>}
                        </label>
                      </div>
                    ) : (
                      <span style={{ color: '#000000' }}>
                        {row.pro_img ? (
                          <img src={backendPath(`/uploads/Product/${row.pro_img}`)} alt="" style={{ width: 48, height: 48, objectFit: 'contain', border: '1px solid #ddd', borderRadius: 6 }} />
                        ) : (
                          '—'
                        )}
                      </span>
                    )}
                  </td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)' }}>
                    {editingId === row.pro_id ? (
                      <div style={{ display: 'flex', gap: 8 }}>
                        <button type="button" onClick={() => saveEdit(row.pro_id)} style={{ ...buttonStyle, padding: '8px 10px', background: 'rgba(90,103,216,0.8)', color: '#ffffff' }}>
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
                        <button type="button" onClick={() => deleteProduct(row.pro_id)} style={{ ...buttonStyle, padding: '8px 10px', background: 'rgba(255,80,80,0.3)', color: '#000000' }}>
                          Delete
                        </button>
                      </div>
                    )}
                  </td>
                </tr>
              ))}
              {filtered.length === 0 ? (
                <tr>
                  <td colSpan={7} style={{ padding: 12, color: '#000000' }}>
                    No products found.
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

