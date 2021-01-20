const colors = require('tailwindcss/colors')

module.exports = {
    purge: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
  darkMode: false, // or 'media' or 'class'
  theme: {
      extend: {
          colors: {
              'light-blue': colors.lightBlue,
              cyan: colors.cyan,
          },
      },
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
