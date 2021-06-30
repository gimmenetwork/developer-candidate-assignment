import React from 'react';
import ReactDOM from 'react-dom';

function Home() {
    return <h2>Home</h2>;
}

if (document.getElementById('app')) {
    ReactDOM.render(<Home/>, document.getElementById('app'));
}