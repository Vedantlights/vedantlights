export const BACKEND_BASE_PATH = (import.meta.env.VITE_BACKEND_BASE_PATH || '').replace(/\/+$/, '');

export function backendPath(pathname) {
  const path = pathname.startsWith('/') ? pathname : `/${pathname}`;
  return `${BACKEND_BASE_PATH}${path}`;
}

export function apiPath(pathname) {
  return backendPath(`/api${pathname.startsWith('/') ? pathname : `/${pathname}`}`);
}

