module.exports = {
    // Example setup for your project:
    // The entry module that requires or imports the rest of your project.
    // Must start with `./`!
    mode: "development",
    entry: './src/entry.js',
    // Place output files in `./dist/my-app.js`
    output: {
        path: __dirname + '/dist',
        filename: 'my-app.js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /(node_modules|bin|assets)/,
                loader: 'babel-loader',
                options: {
                    presets: ['@babel/preset-env'],
                }
            }
        ]
    }
};
