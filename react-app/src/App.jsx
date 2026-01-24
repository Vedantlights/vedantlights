import React, { useState, useEffect, useMemo } from 'react';
import { BrowserRouter as Router, Routes, Route, useMatch, useLocation } from 'react-router-dom';
import Header from './components/Header/Header';
import Footer from './components/Footer/Footer';
import Home from './pages/Home/Home';
import About from './pages/About/About';
import Contact from './pages/Contact/Contact';
import BrandDetails from './pages/BrandDetails/BrandDetails';
import ProductDetails from './pages/ProductDetails/ProductDetails';
import './App.css';
import { apiPath } from './lib/backend';
import AdminLayout from './components/admin/AdminLayout';
import RequireAdmin from './components/admin/RequireAdmin';
import AdminLogin from './pages/admin/AdminLogin';
import AdminDashboard from './pages/admin/AdminDashboard';
import AdminBrands from './pages/admin/AdminBrands';
import AdminCategories from './pages/admin/AdminCategories';
import AdminProducts from './pages/admin/AdminProducts';

function detectBasename() {
  const p = window.location.pathname || '/';
  // When mod_rewrite is disabled, CodeIgniter routes look like:
  //   /public_html/index.php/admin/login
  if (p === '/public_html/index.php' || p.startsWith('/public_html/index.php/')) {
    return '/public_html/index.php';
  }
  // Typical XAMPP folder deployment:
  //   /public_html/admin/login
  if (p === '/public_html' || p.startsWith('/public_html/')) {
    return '/public_html';
  }
  // VirtualHost / root deployment:
  //   /admin/login
  return '';
}

function AppShell() {
  // Use the router's matching logic (honors basename) so admin routes
  // work both on Vite dev (/) and XAMPP (/public_html[/index.php]).
  const isAdminRoute = !!useMatch('/admin/*');
  const [brands, setBrands] = useState([]);

  useEffect(() => {
    fetch(apiPath('/brands'))
      .then((res) => res.json())
      .then((payload) => setBrands(payload?.data ?? []))
      .catch((err) => console.error('Error fetching brands:', err));
  }, []);

  return (
    <div className="App">
      {!isAdminRoute ? <Header brands={brands} /> : null}
      <main className="main-content">
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/aboutus" element={<About />} />
          <Route path="/contactus" element={<Contact />} />
          <Route path="/brandDetails/:brandId/:brandName" element={<BrandDetails />} />
          <Route path="/categoryDetails/:brandId/:brandName/:catId" element={<BrandDetails />} />
          <Route path="/productDetails/:proId" element={<ProductDetails />} />

          <Route path="/admin/login" element={<AdminLogin />} />
          <Route
            path="/admin"
            element={
              <RequireAdmin>
                <AdminLayout />
              </RequireAdmin>
            }
          >
            <Route index element={<AdminDashboard />} />
            <Route path="brands" element={<AdminBrands />} />
            <Route path="categories" element={<AdminCategories />} />
            <Route path="products" element={<AdminProducts />} />
          </Route>
        </Routes>
      </main>
      {!isAdminRoute ? <Footer /> : null}
    </div>
  );
}

function App() {
  const basename = useMemo(() => detectBasename(), []);
  return (
    <Router basename={basename}>
      <AppShell />
    </Router>
  );
}

export default App;
