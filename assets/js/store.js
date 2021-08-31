import {createStore, applyMiddleware, combineReducers} from "redux";
import thunk from "redux-thunk";
import createReducerWithNamedType from "App/reducer";
import constants from "App/constants";

const rootReducer = combineReducers({
    [constants.BOOKS_DATAGRID_NAME]: createReducerWithNamedType(constants.BOOKS_DATAGRID_NAME),
    [constants.READERS_DATAGRID_NAME]: createReducerWithNamedType(constants.READERS_DATAGRID_NAME),
});

const store = createStore(rootReducer, applyMiddleware(thunk));
export default store;
