import React, { useEffect, useMemo, useRef, useState, useCallback } from 'react';
import { adminFetch } from '../../lib/adminFetch';
import { backendPath } from '../../lib/backend';
import { copyTable, downloadTextFile, printTable, toCsv } from '../../lib/tableTools';
import { FaTimes, FaPlus, FaFilePdf, FaArrowUp, FaArrowDown, FaTrash } from 'react-icons/fa';

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
  const [editingPdfFile, setEditingPdfFile] = useState(null);
  const editingPdfInputRef = useRef(null);

  // Multiple PDFs management
  const [pdfModalOpen, setPdfModalOpen] = useState(false);
  const [pdfModalProductId, setPdfModalProductId] = useState(null);
  const [pdfModalProductName, setPdfModalProductName] = useState('');
  const [pdfModalIsNew, setPdfModalIsNew] = useState(false); // true if opened after creating new product
  const [productPdfs, setProductPdfs] = useState([]);
  const [loadingPdfs, setLoadingPdfs] = useState(false);
  const [newPdfName, setNewPdfName] = useState('');
  const [newPdfFileForModal, setNewPdfFileForModal] = useState(null);
  const [newPdfFilesForModal, setNewPdfFilesForModal] = useState([]); // Multiple PDFs
  const newPdfFileModalRef = useRef(null);

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
      const res = await adminFetch('/admin/products', {
        method: 'POST',
        body: form,
      });
      const createdName = pro_name;
      setNewName('');
      setNewDesc('');
      setNewTech('');
      setNewImageFile(null);
      if (newImageInputRef.current) newImageInputRef.current.value = '';
      await load();
      
      // After creating product, find it and open PDF modal
      const updatedProducts = await adminFetch('/admin/products');
      const newProduct = (updatedProducts?.data || []).find(p => p.pro_name === createdName);
      if (newProduct) {
        // Auto-open PDF modal for the new product
        setPdfModalProductId(newProduct.pro_id);
        setPdfModalProductName(newProduct.pro_name || 'Product');
        setPdfModalIsNew(true); // Mark as newly created
        setPdfModalOpen(true);
        setNewPdfName('');
        setNewPdfFileForModal(null);
        if (newPdfFileModalRef.current) newPdfFileModalRef.current.value = '';
        loadProductPdfs(newProduct.pro_id);
      }
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
    setEditingPdfFile(null);
    if (editingImageInputRef.current) editingImageInputRef.current.value = '';
    if (editingPdfInputRef.current) editingPdfInputRef.current.value = '';
  }

  function cancelEdit() {
    setEditingId(null);
    setEditingBrandId('');
    setEditingCatId('');
    setEditingName('');
    setEditingDesc('');
    setEditingTech('');
    setEditingImageFile(null);
    setEditingPdfFile(null);
    if (editingImageInputRef.current) editingImageInputRef.current.value = '';
    if (editingPdfInputRef.current) editingPdfInputRef.current.value = '';
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
      if (editingPdfFile) form.append('product_pdf', editingPdfFile);
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
      // Try DELETE first, fallback to POST route for deployment/server compatibility
      let deleteSuccess = false;
      try {
        await adminFetch(`/admin/products/${id}`, { method: 'DELETE' });
        deleteSuccess = true;
      } catch (deleteError) {
        // If DELETE fails for any reason, try POST route as fallback
        // This handles: 405 (Method Not Allowed), 404 (Not Found), 
        // network errors, CORS issues, and other deployment-specific problems
        console.warn('DELETE request failed, trying POST fallback:', deleteError);
        try {
          await adminFetch(`/admin/products/${id}/delete`, { method: 'POST' });
          deleteSuccess = true;
        } catch (postError) {
          // If POST fallback also fails, throw the original error
          throw deleteError.status ? deleteError : postError;
        }
      }
      
      if (deleteSuccess) {
        await load();
      }
    } catch (e) {
      const errorMsg = e.message || e.statusText || `Failed to delete product (${e.status || 'unknown error'})`;
      setError(errorMsg);
      console.error('Delete product error:', e);
    }
  }

  // Load PDFs for a product
  const loadProductPdfs = useCallback(async (proId) => {
    setLoadingPdfs(true);
    try {
      const res = await adminFetch(`/admin/products/${proId}/pdfs`);
      setProductPdfs(res?.data || []);
    } catch (e) {
      console.error('Failed to load PDFs:', e);
      setProductPdfs([]);
    } finally {
      setLoadingPdfs(false);
    }
  }, []);

  // Open PDF management modal
  function openPdfModal(row) {
    setPdfModalProductId(row.pro_id);
    setPdfModalProductName(row.pro_name || 'Product');
    setPdfModalIsNew(false); // Not a new product
    setPdfModalOpen(true);
    setNewPdfName('');
    setNewPdfFileForModal(null);
    if (newPdfFileModalRef.current) newPdfFileModalRef.current.value = '';
    loadProductPdfs(row.pro_id);
  }

  // Close PDF modal
  function closePdfModal() {
    setPdfModalOpen(false);
    setPdfModalProductId(null);
    setPdfModalProductName('');
    setPdfModalIsNew(false);
    setProductPdfs([]);
    setNewPdfName('');
    setNewPdfFileForModal(null);
    setNewPdfFilesForModal([]);
    if (newPdfFileModalRef.current) newPdfFileModalRef.current.value = '';
  }

  // Add a new PDF to product (single file with custom name)
  async function addPdfToProduct(e) {
    e.preventDefault();
    if (!pdfModalProductId || !newPdfName.trim() || !newPdfFileForModal) {
      alert('Please enter PDF name and select a PDF file');
      return;
    }
    try {
      const form = new FormData();
      form.append('pdf_name', newPdfName.trim());
      form.append('pdf_file', newPdfFileForModal);
      await adminFetch(`/admin/products/${pdfModalProductId}/pdfs`, {
        method: 'POST',
        body: form,
      });
      setNewPdfName('');
      setNewPdfFileForModal(null);
      if (newPdfFileModalRef.current) newPdfFileModalRef.current.value = '';
      await loadProductPdfs(pdfModalProductId);
    } catch (e) {
      alert(e.message || 'Failed to add PDF');
    }
  }

  // Add multiple PDFs to product (using filenames as PDF names)
  async function addMultiplePdfsToProduct(e) {
    e.preventDefault();
    if (!pdfModalProductId || newPdfFilesForModal.length === 0) {
      alert('Please select at least one PDF file');
      return;
    }
    try {
      let successCount = 0;
      let failedFiles = [];
      
      for (const file of newPdfFilesForModal) {
        try {
          const form = new FormData();
          // Use filename without .pdf extension as the PDF name
          const pdfName = file.name.replace(/\.pdf$/i, '');
          form.append('pdf_name', pdfName);
          form.append('pdf_file', file);
          await adminFetch(`/admin/products/${pdfModalProductId}/pdfs`, {
            method: 'POST',
            body: form,
          });
          successCount++;
        } catch (err) {
          failedFiles.push(file.name);
        }
      }
      
      setNewPdfFilesForModal([]);
      if (newPdfFileModalRef.current) newPdfFileModalRef.current.value = '';
      await loadProductPdfs(pdfModalProductId);
      
      if (failedFiles.length > 0) {
        alert(`Added ${successCount} PDF(s). Failed: ${failedFiles.join(', ')}`);
      } else {
        // Optional: show success message
      }
    } catch (e) {
      alert(e.message || 'Failed to add PDFs');
    }
  }

  // Delete a PDF
  async function deletePdf(pdfId) {
    if (!confirm('Delete this PDF?')) return;
    try {
      // Try DELETE first, fallback to POST
      try {
        await adminFetch(`/admin/product-pdfs/${pdfId}`, { method: 'DELETE' });
      } catch {
        await adminFetch(`/admin/product-pdfs/${pdfId}/delete`, { method: 'POST' });
      }
      await loadProductPdfs(pdfModalProductId);
    } catch (e) {
      alert(e.message || 'Failed to delete PDF');
    }
  }

  // Reorder functions for multiple PDF selection
  function movePdfFileUp(index) {
    if (index === 0) return;
    const newFiles = [...newPdfFilesForModal];
    [newFiles[index - 1], newFiles[index]] = [newFiles[index], newFiles[index - 1]];
    setNewPdfFilesForModal(newFiles);
  }

  function movePdfFileDown(index) {
    if (index === newPdfFilesForModal.length - 1) return;
    const newFiles = [...newPdfFilesForModal];
    [newFiles[index], newFiles[index + 1]] = [newFiles[index + 1], newFiles[index]];
    setNewPdfFilesForModal(newFiles);
  }

  function removePdfFile(index) {
    const newFiles = newPdfFilesForModal.filter((_, i) => i !== index);
    setNewPdfFilesForModal(newFiles);
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

      <form onSubmit={createProduct} style={{ marginBottom: 12 }}>
        <div style={{ display: 'flex', gap: 8, alignItems: 'center', flexWrap: 'wrap', marginBottom: 8 }}>
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
            Add Product
          </button>
          <span style={{ fontSize: 12, color: '#666', padding: '8px 0' }}>
            (PDFs can be added after creating the product)
          </span>
          <button type="button" onClick={load} style={buttonStyle}>
            Refresh
          </button>
          <input value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Search…" style={{ ...inputStyle, flex: 1, minWidth: 240 }} />
        </div>
        <div style={{ display: 'flex', gap: 12, flexWrap: 'wrap', alignItems: 'flex-start' }}>
          <div style={{ flex: 1, minWidth: 280 }}>
            <label style={{ display: 'block', fontSize: 13, color: '#000000', marginBottom: 4 }}>Tech (optional)</label>
            <textarea
              value={newTech}
              onChange={(e) => setNewTech(e.target.value)}
              placeholder="Technical specification…"
              rows={5}
              style={{ ...inputStyle, width: '100%', minHeight: 100, resize: 'vertical', fontFamily: 'inherit' }}
            />
          </div>
          <div style={{ flex: 1, minWidth: 280 }}>
            <label style={{ display: 'block', fontSize: 13, color: '#000000', marginBottom: 4 }}>Description (optional)</label>
            <textarea
              value={newDesc}
              onChange={(e) => setNewDesc(e.target.value)}
              placeholder="Product description…"
              rows={5}
              style={{ ...inputStyle, width: '100%', minHeight: 100, resize: 'vertical', fontFamily: 'inherit' }}
            />
          </div>
        </div>
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
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Description</th>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Tech</th>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>Image</th>
                <th style={{ textAlign: 'left', padding: 10, borderBottom: '1px solid rgba(0,0,0,0.1)', color: '#000000' }}>PDFs</th>
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
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)', minWidth: 220 }}>
                    {editingId === row.pro_id ? (
                      <textarea value={editingDesc} onChange={(e) => setEditingDesc(e.target.value)} placeholder="Description" rows={8} style={{ ...inputStyle, width: '100%', minHeight: 160, resize: 'vertical', fontFamily: 'inherit' }} />
                    ) : (
                      <span style={{ color: '#000000' }} title={row.pro_desc || ''}>{row.pro_desc ? (row.pro_desc.length > 60 ? `${row.pro_desc.slice(0, 60)}…` : row.pro_desc) : '—'}</span>
                    )}
                  </td>
                  <td style={{ padding: 10, borderBottom: '1px solid rgba(0,0,0,0.08)', minWidth: 220 }}>
                    {editingId === row.pro_id ? (
                      <textarea value={editingTech} onChange={(e) => setEditingTech(e.target.value)} placeholder="Technical specification" rows={8} style={{ ...inputStyle, width: '100%', minHeight: 160, resize: 'vertical', fontFamily: 'inherit' }} />
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
                    <button
                      type="button"
                      onClick={() => openPdfModal(row)}
                      style={{
                        ...buttonStyle,
                        padding: '6px 10px',
                        display: 'flex',
                        alignItems: 'center',
                        gap: 4,
                        fontSize: 12,
                        background: '#e3f2fd',
                      }}
                    >
                      <FaFilePdf style={{ color: '#d32f2f' }} />
                      Manage PDFs
                    </button>
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
                  <td colSpan={9} style={{ padding: 12, color: '#000000' }}>
                    No products found.
                  </td>
                </tr>
              ) : null}
            </tbody>
          </table>
        </div>
      )}

      {/* PDF Management Modal */}
      {pdfModalOpen && (
        <div
          style={{
            position: 'fixed',
            inset: 0,
            background: 'rgba(0,0,0,0.5)',
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center',
            zIndex: 1000,
          }}
          onClick={closePdfModal}
        >
          <div
            style={{
              background: '#fff',
              borderRadius: 12,
              padding: 24,
              minWidth: 400,
              maxWidth: 600,
              maxHeight: '80vh',
              overflowY: 'auto',
              boxShadow: '0 4px 20px rgba(0,0,0,0.2)',
            }}
            onClick={(e) => e.stopPropagation()}
          >
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 16 }}>
              <h3 style={{ margin: 0, color: '#000' }}>
                {pdfModalIsNew ? 'Product Created!' : 'Manage PDFs'} - {pdfModalProductName}
              </h3>
              <button
                type="button"
                onClick={closePdfModal}
                style={{ background: 'none', border: 'none', cursor: 'pointer', fontSize: 20, color: '#666' }}
              >
                <FaTimes />
              </button>
            </div>

            {/* Success message for new product */}
            {pdfModalIsNew && (
              <div style={{ 
                marginBottom: 16, 
                padding: 12, 
                background: '#e8f5e9', 
                borderRadius: 8, 
                border: '1px solid #a5d6a7',
                color: '#2e7d32'
              }}>
                Product created successfully! Now add your PDF documents below.
              </div>
            )}

            {/* Add multiple PDFs at once */}
            <form onSubmit={addMultiplePdfsToProduct} style={{ marginBottom: 16, padding: 16, background: '#e3f2fd', borderRadius: 8, border: '1px solid #90caf9' }}>
              <h4 style={{ margin: '0 0 12px 0', color: '#1565c0', fontSize: 14 }}>Add Multiple PDFs (Quick Add)</h4>
              <p style={{ margin: '0 0 12px 0', fontSize: 12, color: '#666' }}>
                Select multiple PDF files at once. The filename will be used as the PDF name automatically.
                <br />
                <strong>Tip:</strong> You can reorder files after selecting them to control the upload sequence.
              </p>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
                <input
                  type="file"
                  accept=".pdf,application/pdf"
                  multiple
                  onChange={(e) => {
                    const files = Array.from(e.target.files || []);
                    // Add to existing list instead of replacing
                    setNewPdfFilesForModal(prev => [...prev, ...files]);
                    // Clear input to allow selecting same files again if needed
                    e.target.value = '';
                  }}
                  style={{ ...inputStyle, padding: 8 }}
                />
                {newPdfFilesForModal.length > 0 && (
                  <div style={{ fontSize: 12, color: '#1565c0', background: '#fff', padding: 12, borderRadius: 6, border: '1px solid #90caf9' }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 8 }}>
                      <strong>Selected {newPdfFilesForModal.length} file(s) - Upload order:</strong>
                      <span style={{ fontSize: 11, color: '#666' }}>Use arrows to reorder</span>
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 6 }}>
                      {newPdfFilesForModal.map((file, idx) => (
                        <div 
                          key={idx} 
                          style={{ 
                            display: 'flex', 
                            alignItems: 'center', 
                            gap: 8, 
                            padding: '8px 10px', 
                            background: '#f5f5f5', 
                            borderRadius: 6,
                            border: '1px solid #ddd'
                          }}
                        >
                          <span style={{ 
                            fontWeight: 'bold', 
                            color: '#1976d2', 
                            minWidth: 24, 
                            textAlign: 'center',
                            background: '#e3f2fd',
                            borderRadius: 4,
                            padding: '2px 6px'
                          }}>
                            {idx + 1}
                          </span>
                          <div style={{ flex: 1 }}>
                            <div style={{ color: '#333', fontWeight: 500 }}>{file.name}</div>
                            <div style={{ color: '#666', fontSize: 11 }}>→ Display name: "{file.name.replace(/\.pdf$/i, '')}"</div>
                          </div>
                          <div style={{ display: 'flex', gap: 4 }}>
                            <button
                              type="button"
                              onClick={() => movePdfFileUp(idx)}
                              disabled={idx === 0}
                              style={{
                                ...buttonStyle,
                                padding: '4px 8px',
                                fontSize: 12,
                                opacity: idx === 0 ? 0.4 : 1,
                                cursor: idx === 0 ? 'not-allowed' : 'pointer',
                              }}
                              title="Move up"
                            >
                              <FaArrowUp />
                            </button>
                            <button
                              type="button"
                              onClick={() => movePdfFileDown(idx)}
                              disabled={idx === newPdfFilesForModal.length - 1}
                              style={{
                                ...buttonStyle,
                                padding: '4px 8px',
                                fontSize: 12,
                                opacity: idx === newPdfFilesForModal.length - 1 ? 0.4 : 1,
                                cursor: idx === newPdfFilesForModal.length - 1 ? 'not-allowed' : 'pointer',
                              }}
                              title="Move down"
                            >
                              <FaArrowDown />
                            </button>
                            <button
                              type="button"
                              onClick={() => removePdfFile(idx)}
                              style={{
                                ...buttonStyle,
                                padding: '4px 8px',
                                fontSize: 12,
                                background: 'rgba(255,80,80,0.15)',
                                color: '#d32f2f',
                              }}
                              title="Remove"
                            >
                              <FaTrash />
                            </button>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                )}
                <button
                  type="submit"
                  disabled={newPdfFilesForModal.length === 0}
                  style={{
                    ...buttonStyle,
                    background: newPdfFilesForModal.length > 0 ? '#1976d2' : '#ccc',
                    color: '#fff',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    gap: 6,
                    cursor: newPdfFilesForModal.length > 0 ? 'pointer' : 'not-allowed',
                  }}
                >
                  <FaPlus /> Add {newPdfFilesForModal.length || ''} PDF{newPdfFilesForModal.length !== 1 ? 's' : ''}
                </button>
              </div>
            </form>

            {/* Add single PDF with custom name */}
            <form onSubmit={addPdfToProduct} style={{ marginBottom: 20, padding: 16, background: '#f5f5f5', borderRadius: 8 }}>
              <h4 style={{ margin: '0 0 12px 0', color: '#000', fontSize: 14 }}>Add Single PDF (Custom Name)</h4>
              <p style={{ margin: '0 0 12px 0', fontSize: 12, color: '#666' }}>
                Add a single PDF with a custom display name.
              </p>
              <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
                <input
                  type="text"
                  value={newPdfName}
                  onChange={(e) => setNewPdfName(e.target.value)}
                  placeholder="PDF Name (e.g., Datasheet, Manual)"
                  style={{ ...inputStyle, width: '100%' }}
                />
                <input
                  ref={newPdfFileModalRef}
                  type="file"
                  accept=".pdf,application/pdf"
                  onChange={(e) => {
                    const file = e.target.files?.[0] ?? null;
                    setNewPdfFileForModal(file);
                    // Auto-fill PDF name from filename (without .pdf extension)
                    if (file && file.name) {
                      const nameWithoutExt = file.name.replace(/\.pdf$/i, '');
                      setNewPdfName(nameWithoutExt);
                    }
                  }}
                  style={{ ...inputStyle, padding: 8 }}
                />
                {newPdfFileForModal && (
                  <span style={{ fontSize: 12, color: '#666' }}>Selected: {newPdfFileForModal.name}</span>
                )}
                <button
                  type="submit"
                  style={{
                    ...buttonStyle,
                    background: 'rgba(90,103,216,0.8)',
                    color: '#fff',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    gap: 6,
                  }}
                >
                  <FaPlus /> Add PDF
                </button>
              </div>
            </form>

            {/* Existing PDFs list */}
            <div>
              <h4 style={{ margin: '0 0 12px 0', color: '#000', fontSize: 14 }}>
                Existing PDFs ({productPdfs.length})
              </h4>
              {loadingPdfs ? (
                <div style={{ color: '#666', padding: 12 }}>Loading...</div>
              ) : productPdfs.length === 0 ? (
                <div style={{ color: '#666', padding: 12, background: '#f9f9f9', borderRadius: 6 }}>
                  No PDFs added yet. Add your first PDF above.
                </div>
              ) : (
                <div style={{ display: 'flex', flexDirection: 'column', gap: 8 }}>
                  {productPdfs.map((pdf) => (
                    <div
                      key={pdf.pdf_id}
                      style={{
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'space-between',
                        padding: 12,
                        background: '#f9f9f9',
                        borderRadius: 8,
                        border: '1px solid #eee',
                      }}
                    >
                      <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <FaFilePdf style={{ color: '#d32f2f', fontSize: 20 }} />
                        <div>
                          <div style={{ fontWeight: 500, color: '#000' }}>{pdf.pdf_name}</div>
                          <a
                            href={backendPath(`/uploads/Product/${pdf.pdf_file}`)}
                            target="_blank"
                            rel="noopener noreferrer"
                            style={{ fontSize: 12, color: '#1976d2' }}
                          >
                            View PDF
                          </a>
                        </div>
                      </div>
                      <button
                        type="button"
                        onClick={() => deletePdf(pdf.pdf_id)}
                        style={{
                          ...buttonStyle,
                          padding: '6px 10px',
                          background: 'rgba(255,80,80,0.2)',
                          color: '#d32f2f',
                          fontSize: 12,
                        }}
                      >
                        Delete
                      </button>
                    </div>
                  ))}
                </div>
              )}
            </div>

            <div style={{ marginTop: 20, textAlign: 'right' }}>
              <button type="button" onClick={closePdfModal} style={buttonStyle}>
                Close
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

