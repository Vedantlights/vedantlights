# Quick Start Guide

## Getting Started

1. **Navigate to the React app directory:**
   ```bash
   cd react-app
   ```

2. **Install dependencies (if not already done):**
   ```bash
   npm install
   ```

3. **Start the development server:**
   ```bash
   npm run dev
   ```

4. **Open your browser:**
   - The app will be available at `http://localhost:5173`

## Project Structure

```
react-app/
├── src/
│   ├── components/          # Reusable components
│   │   ├── Button/         # Button component
│   │   ├── Header/         # Header/Navigation
│   │   └── Footer/          # Footer component
│   ├── pages/              # Page components
│   │   ├── Home/           # Home page
│   │   ├── About/          # About page
│   │   ├── Contact/        # Contact page
│   │   ├── BrandDetails/   # Brand details page
│   │   └── ProductDetails/ # Product details page
│   ├── styles/             # Global styles
│   │   ├── variables.css  # CSS variables (colors, spacing)
│   │   └── global.css     # Global styles
│   ├── App.jsx             # Main app component with routing
│   └── main.jsx            # Entry point
├── public/                 # Static assets
└── package.json            # Dependencies
```

## Key Files to Customize

### 1. **Brands Data** (`src/App.jsx`)
   - Currently uses mock data
   - Replace with API call to fetch brands

### 2. **Images**
   - Place images in `public/web_assets/images/`
   - Update paths if your structure differs

### 3. **Colors** (`src/styles/variables.css`)
   - All colors are in CSS variables
   - Easy to customize while maintaining brand

### 4. **Content**
   - Update content in respective page components
   - Home: `src/pages/Home/Home.jsx`
   - About: `src/pages/About/About.jsx`
   - Contact: `src/pages/Contact/Contact.jsx`

## API Integration

To connect to your PHP backend:

1. **Update brands fetch in `App.jsx`:**
   ```javascript
   useEffect(() => {
     fetch('http://your-api-url/api/brands')
       .then(res => res.json())
       .then(data => setBrands(data))
       .catch(err => console.error('Error:', err));
   }, []);
   ```

2. **Update form submission in `Contact.jsx`:**
   ```javascript
   const handleSubmit = async (e) => {
     e.preventDefault();
     const response = await fetch('http://your-api-url/sendmail', {
       method: 'POST',
       headers: { 'Content-Type': 'application/json' },
       body: JSON.stringify(formData)
     });
     // Handle response
   };
   ```

## Building for Production

```bash
npm run build
```

The built files will be in the `dist/` directory.

## Preview Production Build

```bash
npm run preview
```

## Common Issues

### Images Not Loading
- Ensure images are in `public/web_assets/images/`
- Check image paths in components
- Fallback images are provided for missing images

### Styling Issues
- Clear browser cache
- Check CSS variable values in `variables.css`
- Ensure all CSS files are imported

### Routing Issues
- Ensure React Router is properly set up
- Check route paths match your navigation links

## Next Steps

1. ✅ Test all pages and functionality
2. ✅ Integrate with your backend API
3. ✅ Add real product/brand data
4. ✅ Optimize images
5. ✅ Add SEO meta tags (React Helmet)
6. ✅ Set up analytics
7. ✅ Deploy to production

---

**Need Help?** Check the README.md and IMPROVEMENTS.md files for more details.
