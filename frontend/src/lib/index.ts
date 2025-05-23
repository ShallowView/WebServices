// place files you want to import through the `$lib` alias in this folder.

export const getRandomVibrantColor = () => {
  // Generate random RGB values with a bias towards vibrant colors
  const r = Math.floor(Math.random() * 156) + 100; // Range: 100-255
  const g = Math.floor(Math.random() * 156) + 100; // Range: 100-255
  const b = Math.floor(Math.random() * 156) + 100; // Range: 100-255

  return `rgb(${r}, ${g}, ${b})`;
}