import React from 'react';
import Dashboard from "App/Dashboard";
import store from "App/store";
import { Provider } from "react-redux";

function App() {
    return (
        <Provider store={store}>
            <React.Fragment>
                <Dashboard />
            </React.Fragment>
        </Provider>
    );
}

export default App;