import { apiPath } from './backend';

async function readJsonSafe(res) {
  const text = await res.text();
  if (!text) return null;
  try {
    return JSON.parse(text);
  } catch {
    return { raw: text };
  }
}

export async function adminFetch(pathname, options = {}) {
  const isFormData = typeof FormData !== 'undefined' && options.body instanceof FormData;
  const hasBody = options.body !== undefined && options.body !== null;

  const res = await fetch(apiPath(pathname), {
    credentials: 'include',
    ...options,
    headers: {
      Accept: 'application/json',
      ...(hasBody && !isFormData ? { 'Content-Type': 'application/json' } : null),
      ...(options.headers || {}),
    },
  });

  const payload = await readJsonSafe(res);
  if (!res.ok) {
    const message =
      payload?.error ||
      payload?.message ||
      (typeof payload?.raw === 'string' ? payload.raw : null) ||
      `Request failed (${res.status})`;
    const err = new Error(message);
    err.status = res.status;
    err.payload = payload;
    throw err;
  }

  return payload;
}

