import * as types from "App/actions/types";
import axios from "axios";
import { get } from "lodash";
import {getFormData} from "App/utils";

/**
 * Get collection of items
 *
 * @param url
 * @return {function(*): Promise<void>}
 */
export const getItems = (url, key) => {
    return function(dispatch) {
        dispatch(startCollectionRequest(key));
        return axios({
            method: 'GET',
            url: url,
        })
            .then(response => {
                dispatch({
                    type: types.STORE_COLLECTION + `_${key}`,
                    data: {
                        items: get(response, 'data.items'),
                        count: get(response, 'data.totalItemCount'),
                    }
                });
                dispatch(endCollectionRequest(key));
            })
            .catch(error => {
                dispatch(endCollectionRequest(key));
            })
        ;
    };
}

export const getItem = (url, key) => {
    return function(dispatch) {
        dispatch(startItemRequest(key));
        return axios({
            method: 'GET',
            url: url,
        })
            .then(response => {
                dispatch({
                    type: types.STORE_ITEM + `_${key}`,
                    data: get(response, 'data')
                });
                dispatch(endItemRequest(key));
            })
            .catch(error => {
                dispatch(endItemRequest(key));
            });
    };
}

export const clearItem = (key) => dispatch => {
    dispatch(clearItemRequest(key));
}

export const createItem = (url, data) => dispatch => {
    const values = getFormData(data);
    return axios.post(url, values).then(function (response) {
            return response;
        }).catch(function (error) {
            return Promise.reject(error);
        });
}

export const updateItem = (url, data) => dispatch => {
    const config = { headers: {'Content-Type': 'application/json'} };
    return axios.put(url, data, config).then(function (response) {
            return response;
        }).catch(function (error) {
            return Promise.reject(error);
        });
}

export const patchItem = (url, data) => dispatch => {
    const config = { headers: {'Content-Type': 'application/json'} };
    return axios.patch(url, data, config).then(function (response) {
        return response;
    }).catch(function (error) {
        return Promise.reject(error);
    });
}

export const deleteItem = (url, key) => {
    return function(dispatch) {
        dispatch({
            type: types.START_COLLECTION_LOADING + `_${key}`,
        });
        return axios({
            method: 'DELETE',
            url: url,
        })
            .then(response => {
                dispatch({
                    type: types.FORCE_RELOAD + `_${key}`,
                });
            })
            .catch(error => {
                dispatch({
                    type: types.END_COLLECTION_LOADING + `_${key}`,
                });
            })
            ;
    };
}

export const getGenres = url => dispatch => {
    return axios({
        method: 'GET',
        url: url,
    })
        .then(response => {
            dispatch({
                type: types.STORE_GENRES,
                data: get(response, 'data')
            });
        })
        .catch(error => {
            console.log(error);
        });
}

export const getReaders = (url, key) => dispatch => {
    dispatch({
        type: types.START_COLLECTION_LOADING + `_${key}`,
    });

    return axios({
        method: 'GET',
        url: url,
    })
        .then(response => {
            dispatch({
                type: types.END_COLLECTION_LOADING + `_${key}`,
            });
            dispatch({
                type: types.STORE_READERS,
                data: get(response, ['data', 'items'])
            });
        })
        .catch(error => {
            dispatch({
                type: types.END_COLLECTION_LOADING + `_${key}`,
            });
            return Promise.reject(error);
        });
}

const startCollectionRequest = (key) => {
    return {
        type: types.START_COLLECTION_REQUEST + `_${key}`,
    };
}

const endCollectionRequest = (key) => {
    return {
        type: types.END_COLLECTION_REQUEST + `_${key}`,
    };
}

const startItemRequest = (key) => {
    return {
        type: types.START_ITEM_REQUEST + `_${key}`,
    };
}

const endItemRequest = (key) => {
    return {
        type: types.END_ITEM_REQUEST + `_${key}`,
    };
}

const clearItemRequest = (key) => {
    return {
        type: types.CLEAR_ITEM_REQUEST + `_${key}`,
    };
}