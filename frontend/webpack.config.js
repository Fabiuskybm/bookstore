import path from 'path';

export default {
    mode: 'development',
    entry: './src/js/app.js',
    output: {
        filename: 'main.js',
        path: path.resolve('../public/assets/js'),
        clean: true
    },
    module: {
        rules: [
            {
                test: /\.scss$/i,
                use: ['style-loader', 'css-loader', 'sass-loader']
            }
        ]
    }
}