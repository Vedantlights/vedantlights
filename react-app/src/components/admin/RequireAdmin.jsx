import React, { useEffect, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';
import { adminFetch } from '../../lib/adminFetch';

export default function RequireAdmin({ children }) {
  const navigate = useNavigate();
  const location = useLocation();
  const [state, setState] = useState({ loading: true });

  useEffect(() => {
    let cancelled = false;

    (async () => {
      try {
        await adminFetch('/admin/me');
        if (!cancelled) setState({ loading: false });
      } catch (err) {
        if (!cancelled) {
          setState({ loading: false });
          navigate('/admin/login', { replace: true, state: { from: location.pathname } });
        }
      }
    })();

    return () => {
      cancelled = true;
    };
  }, [navigate, location.pathname]);

  if (state.loading) return <div style={{ padding: 16 }}>Checking session…</div>;
  return children;
}

