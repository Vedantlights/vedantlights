import React, { useMemo, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { adminFetch } from '../../lib/adminFetch';

export default function AdminLogin() {
  const navigate = useNavigate();
  const location = useLocation();
  const from = useMemo(() => location.state?.from || '/admin', [location.state]);

  const [user_name, setUserName] = useState('');
  const [password, setPassword] = useState('');
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState('');

  async function onSubmit(e) {
    e.preventDefault();
    setError('');
    setBusy(true);
    try {
      const payload = await adminFetch('/admin/login', {
        method: 'POST',
        body: JSON.stringify({ user_name, password }),
      });
      if (payload?.txt_code === 101) {
        navigate(from, { replace: true });
        return;
      }
      setError(payload?.message || 'Login failed');
    } catch (err) {
      setError(err.message || 'Login failed');
    } finally {
      setBusy(false);
    }
  }

  return (
    <div style={{ minHeight: '100vh', display: 'grid', placeItems: 'center', background: '#ffffff' }}>
      <form
        onSubmit={onSubmit}
        style={{
          width: 360,
          background: '#ffffff',
          color: '#000000',
          border: '1px solid rgba(0,0,0,0.2)',
          borderRadius: 14,
          padding: 18,
        }}
      >
        <h2 style={{ margin: '0 0 12px', color: '#000000' }}>Admin Login</h2>

        <label style={{ display: 'block', marginTop: 10, fontSize: 13, color: '#000000' }}>Username</label>
        <input
          value={user_name}
          onChange={(e) => setUserName(e.target.value)}
          autoComplete="username"
          style={{ width: '100%', marginTop: 6, padding: 10, borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#ffffff', color: '#000000' }}
        />

        <label style={{ display: 'block', marginTop: 10, fontSize: 13, color: '#000000' }}>Password</label>
        <input
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          type="password"
          autoComplete="current-password"
          style={{ width: '100%', marginTop: 6, padding: 10, borderRadius: 10, border: '1px solid rgba(0,0,0,0.2)', background: '#ffffff', color: '#000000' }}
        />

        {error ? <div style={{ marginTop: 10, color: '#d32f2f' }}>{error}</div> : null}

        <button
          type="submit"
          disabled={busy}
          style={{
            marginTop: 14,
            width: '100%',
            padding: 10,
            borderRadius: 10,
            border: '1px solid rgba(0,0,0,0.2)',
            background: 'rgba(90,103,216,0.8)',
            color: '#ffffff',
            cursor: 'pointer',
          }}
        >
          {busy ? 'Signing in…' : 'Sign in'}
        </button>
      </form>
    </div>
  );
}

