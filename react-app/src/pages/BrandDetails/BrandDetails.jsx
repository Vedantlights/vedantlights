import React, { useEffect, useMemo, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { motion } from 'framer-motion';
import Button from '../../components/Button/Button';
import './BrandDetails.css';
import { apiPath, backendPath } from '../../lib/backend';

const BrandDetails = () => {
  const { brandId, brandName, catId } = useParams();
  const [categories, setCategories] = useState([]);
  const [products, setProducts] = useState([]);

  const brandIdNum = useMemo(() => Number(brandId), [brandId]);
  const catIdNum = useMemo(() => (catId ? Number(catId) : null), [catId]);

  useEffect(() => {
    if (!Number.isFinite(brandIdNum)) return;

    fetch(apiPath(`/brands/${brandIdNum}/categories`))
      .then((res) => res.json())
      .then((payload) => setCategories(payload?.data ?? []))
      .catch((err) => console.error('Error fetching categories:', err));
  }, [brandIdNum]);

  useEffect(() => {
    if (!Number.isFinite(brandIdNum)) return;

    const qs = catIdNum ? `?catId=${encodeURIComponent(catIdNum)}` : '';

    fetch(`${apiPath(`/brands/${brandIdNum}/products`)}${qs}`)
      .then((res) => res.json())
      .then((payload) => setProducts(payload?.data ?? []))
      .catch((err) => console.error('Error fetching products:', err));
  }, [brandIdNum, catIdNum]);

  return (
    <div className="brand-details-page">
      {/* Breadcrumb */}
      <section className="brand-details-breadcrumb-section">
        <div className="container">
          <motion.div
            className="brand-details-breadcrumb-content"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <span className="brand-details-breadcrumb-bg-text">Brand</span>
            <h1 className="brand-details-breadcrumb-title">{brandName || 'Brand'}</h1>
            <div className="brand-details-breadcrumb-links">
              <Link to="/">HOME /</Link>
              <span className="brand-details-active">BRAND</span>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Categories Section */}
      <section className="brand-details-categories-section section-gap">
        <div className="container">
          <motion.div
            className="brand-details-categories-grid"
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            {categories.map((category, index) => (
              <motion.div
                key={category.cat_id}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
              >
                <Link
                  to={`/categoryDetails/${brandId}/${brandName}/${category.cat_id}`}
                  className="brand-details-category-card"
                >
                  <h6>{category.caterogyName}</h6>
                </Link>
              </motion.div>
            ))}
            {/* Show All Card */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.5, delay: categories.length * 0.1 }}
            >
              <Link
                to={`/brandDetails/${brandId}/${brandName}`}
                className="brand-details-category-card brand-details-show-all-card"
              >
                <h6>Show All</h6>
              </Link>
            </motion.div>
          </motion.div>
        </div>
      </section>

      {/* Products Section */}
      <section className="brand-details-products-section section-gap">
        <div className="container">
          <motion.div
            className="brand-details-products-grid"
            initial={{ opacity: 0, y: 20 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
          >
            {products.map((product, index) => (
              <motion.div
                key={product.pro_id}
                className="brand-details-product-card"
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: index * 0.1 }}
                whileHover={{ y: -8, scale: 1.02 }}
              >
                <Link
                  to={`/productDetails/${product.pro_id}`}
                  className="brand-details-product-image"
                >
                  <img
                    src={backendPath(`/uploads/Product/${product.pro_img}`)}
                    alt={product.pro_name}
                    onError={(e) => {
                      e.target.src = 'https://via.placeholder.com/300x200?text=' + product.pro_name;
                    }}
                  />
                </Link>
                <div className="brand-details-product-content">
                  <Link to={`/productDetails/${product.pro_id}`}>
                    <h6 className="brand-details-product-title">{product.pro_name}</h6>
                  </Link>
                  <Button
                    to="/contactus"
                    variant="primary"
                    size="small"
                  >
                    Get Quote
                  </Button>
                </div>
              </motion.div>
            ))}
          </motion.div>
        </div>
      </section>
    </div>
  );
};

export default BrandDetails;
