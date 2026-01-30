import React, { useEffect, useMemo, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import Button from '../../components/Button/Button';
import './ProductDetails.css';
import { apiPath, backendPath } from '../../lib/backend';
import { FaFilePdf } from 'react-icons/fa';

const ProductDetails = () => {
  const { proId } = useParams();

  const proIdNum = useMemo(() => Number(proId), [proId]);
  const [product, setProduct] = useState(null);
  const [isPdfPopupOpen, setIsPdfPopupOpen] = useState(false);

  // Get PDFs array (from new multiple PDFs system)
  const productPdfs = useMemo(() => {
    const pdfs = product?.pdfs || [];
    // Also include legacy single PDF if exists and no multiple PDFs
    if (pdfs.length === 0 && product?.pro_pdf) {
      return [{ pdf_id: 'legacy', pdf_name: 'Product PDF', pdf_file: product.pro_pdf }];
    }
    return pdfs;
  }, [product]);

  useEffect(() => {
    if (!Number.isFinite(proIdNum)) return;

    fetch(apiPath(`/products/${proIdNum}`))
      .then((res) => res.json())
      .then((payload) => setProduct(payload?.data ?? null))
      .catch((err) => console.error('Error fetching product:', err));
  }, [proIdNum]);

  return (
    <div className="product-details-page">
      {/* Breadcrumb */}
      <section className="product-details-breadcrumb-section">
        <div className="container">
          <motion.div
            className="product-details-breadcrumb-content"
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6 }}
          >
            <span className="product-details-breadcrumb-bg-text">Product</span>
            <h1 className="product-details-breadcrumb-title">{product?.pro_name || 'Product'}</h1>
            <div className="product-details-breadcrumb-links">
              <Link to="/">HOME /</Link>
              <span className="product-details-active">PRODUCT</span>
            </div>
          </motion.div>
        </div>
      </section>

      {/* Product Details */}
      <section className="product-details-section section-gap">
        <div className="container">
          <div className="product-details-wrapper">
            <motion.div
              className="product-details-image-area"
              initial={{ opacity: 0, x: -30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <div className="product-details-main-image">
                <img
                  src={product?.pro_img ? backendPath(`/uploads/Product/${product.pro_img}`) : 'https://via.placeholder.com/600x600?text=Product'}
                  alt={product?.pro_name || 'Product'}
                  onError={(e) => {
                    e.target.src = 'https://via.placeholder.com/600x600?text=' + (product?.pro_name || 'Product');
                  }}
                />
              </div>
            </motion.div>

            <motion.div
              className="product-details-info-area"
              initial={{ opacity: 0, x: 30 }}
              whileInView={{ opacity: 1, x: 0 }}
              viewport={{ once: true }}
              transition={{ duration: 0.6 }}
            >
              <h2 className="product-details-title">{product?.pro_name || 'Product'}</h2>
              
              <div className="product-details-specs-section">
                <h3 className="product-details-specs-title">Technical Specification</h3>
                <div 
                  className="product-details-specs"
                  dangerouslySetInnerHTML={{ __html: product?.pro_tech || '' }}
                />
              </div>

              {productPdfs.length > 0 && (
                <div className="product-details-pdf-area">
                  {productPdfs.length === 1 ? (
                    // Single PDF - direct download
                    <Button
                      href={backendPath(`/uploads/Product/${productPdfs[0].pdf_file}`)}
                      variant="secondary"
                      size="large"
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      Download PDF
                    </Button>
                  ) : (
                    // Multiple PDFs - show popup
                    <Button
                      variant="secondary"
                      size="large"
                      onClick={() => setIsPdfPopupOpen(true)}
                    >
                      Download Datasheet c({productPdfs.length})
                    </Button>
                  )}
                </div>
              )}

              <div className="product-details-actions">
                <Button
                  to="/contactus"
                  variant="primary"
                  size="large"
                >
                  Get Quote
                </Button>
                <Button
                  href={`https://api.whatsapp.com/send/?phone=%2B917709298685&text=${encodeURIComponent(`Hi, I'm interested in "${product?.pro_name || 'this product'}". Please share details.`)}&type=phone_number&app_absent=0`}
                  variant="secondary"
                  size="large"
                >
                  Contact on WhatsApp
                </Button>
              </div>
            </motion.div>
          </div>

          {/* Product Description Tabs */}
          <motion.div
            className="product-details-description-section"
            initial={{ opacity: 0, y: 30 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.2 }}
          >
            <div className="product-details-description-tabs">
              <div className="product-details-tab-header">
                <button className="product-details-tab-button product-details-tab-button-active">Description</button>
              </div>
              <div className="product-details-tab-content">
                <div 
                  className="product-details-tab-pane product-details-tab-pane-active"
                  dangerouslySetInnerHTML={{ __html: product?.pro_desc || '' }}
                />
              </div>
            </div>
          </motion.div>
        </div>
      </section>

      {/* PDF Popup - Similar to brochure popup */}
      <AnimatePresence>
        {isPdfPopupOpen && (
          <motion.div
            className="product-pdf-overlay"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={() => setIsPdfPopupOpen(false)}
          >
            <motion.div
              className="product-pdf-popup"
              initial={{ scale: 0.9, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.9, opacity: 0 }}
              onClick={(e) => e.stopPropagation()}
            >
              <h3>Download PDFs</h3>
              <div className="product-pdf-list">
                {productPdfs.map((pdf, index) => (
                  <div key={pdf.pdf_id || index} className="product-pdf-item">
                    <div className="product-pdf-item-icon">
                      <FaFilePdf />
                    </div>
                    <h6>{pdf.pdf_name}</h6>
                    <a
                      href={backendPath(`/uploads/Product/${pdf.pdf_file}`)}
                      download
                      target="_blank"
                      rel="noopener noreferrer"
                      className="product-pdf-download-btn"
                    >
                      Download
                    </a>
                  </div>
                ))}
              </div>
              <Button onClick={() => setIsPdfPopupOpen(false)}>Close</Button>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};

export default ProductDetails;
