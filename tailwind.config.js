/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './wp-content/themes/sejmownik/**/*.php',
    './wp-content/plugins/sejm-api/**/*.php',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        montserrat: ['Montserrat', 'sans-serif'],
      },
      colors: {
        'parlament-blue': '#0a3868',
        'parlament-red': '#d2232a',
        'parlament-gold': '#f0c14b',
      },
      backgroundImage: {
        'parlament-gradient': 'linear-gradient(to right, #d2232a, #96172e)',
      }
    },
  },
  plugins: [],
}
