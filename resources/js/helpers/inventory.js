// Simple helper to fetch inventory transactions for a variant or product
export async function fetchInventoryTransactions({ variant_id = null, product_id = null } = {}) {
  if (!variant_id && !product_id) throw new Error('variant_id or product_id required');

  const body = new URLSearchParams();
  if (variant_id) body.append('variant_id', variant_id);
  if (product_id) body.append('product_id', product_id);

  const res = await fetch('/inventory/transactions', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body.toString(),
    credentials: 'same-origin'
  });

  if (!res.ok) {
    const err = await res.json().catch(() => null);
    throw new Error(err?.message || 'Failed to fetch transactions');
  }

  const json = await res.json();
  if (!json.success) throw new Error(json.message || 'Failed to fetch transactions');
  return json.data;
}

export async function reserveStock(payload = {}) {
  const res = await fetch('/inventory/reserve', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(payload).toString(),
    credentials: 'same-origin'
  });

  if (!res.ok) throw new Error('Failed to reserve stock');
  return res.json();
}

export async function releaseReservation(payload = {}) {
  const res = await fetch('/inventory/release', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(payload).toString(),
    credentials: 'same-origin'
  });

  if (!res.ok) throw new Error('Failed to release reservation');
  return res.json();
}

export async function processReturn(payload = {}) {
  const res = await fetch('/inventory/return', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(payload).toString(),
    credentials: 'same-origin'
  });

  if (!res.ok) throw new Error('Failed to process return');
  return res.json();
}
