Frontend dev notes

Run the development environment:

1. Start the mock API server (optional - frontend will fallback to local mock data if not running):

```bash
cd portal-tech/frontend
npm install
npm run mock
```

2. Start the Vite dev server:

```bash
cd portal-tech/frontend
npm run dev
```

Open the site at the local URL printed by Vite (usually http://localhost:5173 or next available port).

Files of interest:
- `src/views/HomeView.vue` — main view using components
- `src/components/*` — UI components (ProductCard, RankingList, ComparisonTable, CTAButton, PriceBadge)
- `src/services/mockApi.js` — fetches from local mock server or returns fallback data
- `mock-server/server.cjs` — simple Express mock API
