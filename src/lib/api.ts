/**
 * API base: VITE_API_URL in production (e.g. https://api.example.com).
 * Local dev: leave unset and use Vite proxy → PHP container (see vite.config.ts).
 */
const rawBase = (import.meta.env.VITE_API_URL as string | undefined) || '';

export function apiUrl(path: string): string {
  const p = path.startsWith('/') ? path : `/${path}`;
  if (rawBase) {
    return `${rawBase.replace(/\/$/, '')}${p}`;
  }
  return p;
}

export async function apiFetch(path: string, init?: RequestInit): Promise<Response> {
  return fetch(apiUrl(path), {
    ...init,
    credentials: 'include',
    headers: {
      ...(init?.headers as Record<string, string>),
    },
  });
}

export async function apiJson<T>(path: string, init?: RequestInit): Promise<T> {
  const res = await apiFetch(path, init);
  const data = (await res.json()) as T;
  return data;
}

export async function apiReadJsonBody<T>(res: Response): Promise<T> {
  return res.json() as Promise<T>;
}
