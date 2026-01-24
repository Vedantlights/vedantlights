function escapeCsvCell(v) {
  const s = v == null ? '' : String(v);
  if (/[",\n\r]/.test(s)) return `"${s.replace(/"/g, '""')}"`;
  return s;
}

export function toCsv(rows, columns) {
  const header = columns.map((c) => escapeCsvCell(c.label)).join(',');
  const lines = rows.map((row) => columns.map((c) => escapeCsvCell(c.get(row))).join(','));
  return [header, ...lines].join('\r\n');
}

export function downloadTextFile(filename, text, mime = 'text/plain;charset=utf-8') {
  const blob = new Blob([text], { type: mime });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}

export async function copyTable(rows, columns) {
  const header = columns.map((c) => String(c.label)).join('\t');
  const lines = rows.map((row) => columns.map((c) => String(c.get(row) ?? '')).join('\t'));
  const text = [header, ...lines].join('\n');
  await navigator.clipboard.writeText(text);
  return text;
}

export function printTable(title, rows, columns) {
  const w = window.open('', '_blank', 'noopener,noreferrer');
  if (!w) return;

  const css = `
    body { font-family: Arial, sans-serif; padding: 16px; }
    h1 { font-size: 18px; margin: 0 0 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 8px; font-size: 12px; }
    th { background: #f6f6f6; text-align: left; }
  `;

  const thead = `<tr>${columns.map((c) => `<th>${String(c.label)}</th>`).join('')}</tr>`;
  const tbody = rows
    .map((row) => `<tr>${columns.map((c) => `<td>${String(c.get(row) ?? '')}</td>`).join('')}</tr>`)
    .join('');

  w.document.open();
  w.document.write(`<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>${title}</title>
    <style>${css}</style>
  </head>
  <body>
    <h1>${title}</h1>
    <table>
      <thead>${thead}</thead>
      <tbody>${tbody}</tbody>
    </table>
    <script>window.onload = () => { window.print(); };</script>
  </body>
</html>`);
  w.document.close();
}

