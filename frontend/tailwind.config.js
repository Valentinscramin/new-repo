/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        dark: {
          bg: '#0f0f0f',
          card: '#1a1a1a',
          border: '#2a2a2a',
        },
        neon: {
          green: '#00ff88',
          blue: '#00d4ff',
        }
      }
    },
  },
  plugins: [],
}
