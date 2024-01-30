/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './src/**/*.{js,jsx,ts,tsx,vue}',
    './public/**/*.php',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('daisyui')
  ],
}

