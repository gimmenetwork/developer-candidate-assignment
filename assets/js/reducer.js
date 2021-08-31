import * as types from "App/actions/types";

const initialState = {
    items: [],
    count: 0,
    item: null,
    collectionLoading: false,
    itemLoading: false,
    formLoading: false,
    forceReload: false,
    genres: [],
    readers: [],
}
export default function createReducerWithNamedType(name = '') {
    return function appReducer(state = initialState, action) {
        switch (action.type) {
            case types.START_COLLECTION_REQUEST + `_${name}`: {
                return {
                    ...state,
                    items: [],
                    collectionLoading: true,
                    forceReload: false,
                }
            }
            case types.START_ITEM_REQUEST + `_${name}`: {
                return {
                    ...state,
                    item: null,
                    collectionLoading: true,
                    forceReload: false,
                }
            }
            case types.STORE_COLLECTION + `_${name}`: {
                return {
                    ...state,
                    items: action.data.items,
                    count: action.data.count,
                    forceReload: false,
                }
            }
            case types.STORE_ITEM + `_${name}`: {
                return {
                    ...state,
                    item: action.data,
                    forceReload: false,
                }
            }
            case types.END_COLLECTION_REQUEST + `_${name}`: {
                return {
                    ...state,
                    collectionLoading: false,
                    forceReload: false,
                }
            }
            case types.END_ITEM_REQUEST + `_${name}`: {
                return {
                    ...state,
                    collectionLoading: false,
                    forceReload: false,
                }
            }
            case types.CLEAR_ITEM_REQUEST + `_${name}`: {
                return {
                    ...state,
                    item: null,
                }
            }
            case types.START_COLLECTION_LOADING + `_${name}`: {
                return {
                    ...state,
                    collectionLoading: true,
                    forceReload: false,
                }
            }
            case types.END_COLLECTION_LOADING + `_${name}`: {
                return {
                    ...state,
                    collectionLoading: false,
                    forceReload: false,
                }
            }
            case types.FORCE_RELOAD + `_${name}`: {
                return {
                    ...state,
                    forceReload: true,
                }
            }
            case types.STORE_GENRES: {
                return {
                    ...state,
                    genres: action.data,
                }
            }
            case types.STORE_READERS: {
                return {
                    ...state,
                    readers: action.data,
                }
            }
            default:
                return state
        }
    }
}