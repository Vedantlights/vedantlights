import React, { useEffect, useState } from 'react';
import { adminFetch } from '../../lib/adminFetch';

export default function AdminDashboard() {
  const [data, setData] = useState(null);
  const [error, setError] = useState('');

  useEffect(() => {
    adminFetch('/admin/stats')
      .then((payload) => setData(payload?.data || null))
      .catch((e) => setError(e.message || 'Failed to load stats'));
  }, []);

  return (
    <div>
      <h2 style={{ marginTop: 0, color: '#000000' }}>Dashboard</h2>
      {error ? <div style={{ color: '#d32f2f' }}>{error}</div> : null}
      {!data ? (
        <div style={{ color: '#000000' }}>Loading…</div>
      ) : (
        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', gap: 12 }}>
          <div style={{ background: '#f5f5f5', border: '1px solid rgba(0,0,0,0.1)', borderRadius: 12, padding: 14, color: '#000000' }}>
            <div style={{ opacity: 0.7, color: '#000000' }}>Products</div>
            <div style={{ fontSize: 28, fontWeight: 700, color: '#000000' }}>{data.totalProduct}</div>
          </div>
          <div style={{ background: '#f5f5f5', border: '1px solid rgba(0,0,0,0.1)', borderRadius: 12, padding: 14, color: '#000000' }}>
            <div style={{ opacity: 0.7, color: '#000000' }}>Brands</div>
            <div style={{ fontSize: 28, fontWeight: 700, color: '#000000' }}>{data.totalbrand}</div>
          </div>
          <div style={{ background: '#f5f5f5', border: '1px solid rgba(0,0,0,0.1)', borderRadius: 12, padding: 14, color: '#000000' }}>
            <div style={{ opacity: 0.7, color: '#000000' }}>Categories</div>
            <div style={{ fontSize: 28, fontWeight: 700, color: '#000000' }}>{data.totalcategory}</div>
          </div>
        </div>
      )}
    </div>
  );
}

