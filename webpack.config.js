const path = require('path');
module.exports = {
  mode: 'production',
  entry: './frontend/index.js',
  output: {
    path: path.resolve(__dirname, 'public/assets'),
    filename: 'bundle.js',
  },
  module: {
    rules: [
      {
        test: /\.js$/i,
        include: path.resolve(__dirname, 'frontend'),
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
          },
        },
      },
      {
        test: /\.css$/i,
        include: path.resolve(__dirname, 'frontend'),
        use: ['style-loader', 'css-loader', 'postcss-loader'],
      },
    ],
  },
};
