import React from 'react';
import { NavLink, Outlet, useNavigate } from 'react-router-dom';
import { adminFetch } from '../../lib/adminFetch';
import './AdminLayout.css';

export default function AdminLayout() {
  const navigate = useNavigate();

  async function onLogout() {
    try {
      await adminFetch('/admin/logout', { method: 'POST' });
    } finally {
      navigate('/admin/login');
    }
  }

  return (
    <div className="admin-shell">
      <aside className="admin-sidebar">
        <div className="admin-brand">Admin</div>
        <nav className="admin-nav">
          <NavLink to="/admin" end className={({ isActive }) => (isActive ? 'active' : '')}>
            Dashboard
          </NavLink>
          <NavLink to="/admin/brands" className={({ isActive }) => (isActive ? 'active' : '')}>
            Brands
          </NavLink>
          <NavLink to="/admin/categories" className={({ isActive }) => (isActive ? 'active' : '')}>
            Categories
          </NavLink>
          <NavLink to="/admin/products" className={({ isActive }) => (isActive ? 'active' : '')}>
            Products
          </NavLink>
        </nav>
        <button className="admin-logout" onClick={onLogout}>
          Logout
        </button>
      </aside>

      <section className="admin-main">
        <Outlet />
      </section>
    </div>
  );
}

