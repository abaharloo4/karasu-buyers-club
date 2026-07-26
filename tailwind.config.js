/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./src/**/*.{js,jsx,ts,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        karasu: {
          50: '#f0f7ff',
          100: '#e0effe',
          500: '#0284c7',
          600: '#0369a1',
          700: '#075985',
          800: '#0c4a6e',
          900: '#0c3a59',
          gold: '#f59e0b',
          silver: '#94a3b8',
          bronze: '#d97706',
          diamond: '#38bdf8',
        },
      },
    },
  },
  plugins: [],
};
