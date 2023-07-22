/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./src/**/*.{html,js,php}", "./templates/**/*.twig"],
  theme: {
    extend: { 
      colors: {
      main: "#1f312f",
      main2: "#6c7776",
      main3: "#C86E04",
      main4: "#e4e9e8",
    },
  },
  },
  plugins: [],
}
