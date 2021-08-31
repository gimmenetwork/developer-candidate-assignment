import constants from "App/constants";
import BookForm from "App/form/BookForm";
import ReaderForm from "App/form/ReaderForm";
import LeaseBook from "App/form/LeaseBook";

const bookDatagridConfiguration = {
    columns: [
        { field: 'id', headerName: 'id', width: 150, sortable: false, filterable: false },
        { field: 'col2', headerName: 'name', width: 150, sortable: false, filterable: true },
        { field: 'col3', headerName: 'author', width: 150, sortable: false, filterable: false },
        { field: 'col4', headerName: 'genre', width: 150, sortable: false, filterable: true },
        { field: 'col5', headerName: 'is available', width: 150, sortable: false, filterable: false },
    ],
    mapping: {
        id: 'id',
        col2: 'name',
        col3: 'author',
        col4: 'genre.name',
        col5: 'isAvailable',
    },
    filterMapping: {
        col2: 'name',
        col4: 'genre',
    },
    url: constants.BOOKS_API_COLLECTION_URL,
    itemUrl: constants.BOOKS_API_ITEM_URL,
    formName: BookForm,
    actions: [
        {
            name: 'Lease',
            formName: LeaseBook
        }
    ]

};

const readerDatagridConfiguration = {
    columns: [
        { field: 'id', headerName: 'id', width: 150, sortable: false, filterable: false },
        { field: 'col2', headerName: 'name', width: 150, sortable: false, filterable: false },
    ],
    mapping: {
        id: 'id',
        col2: 'name',
    },
    filterMapping: {},
    url: constants.READERS_API_COLLECTION_URL,
    itemUrl: constants.READERS_API_ITEM_URL,
    formName: ReaderForm,
    actions: [],
};

export default {
    [constants.BOOKS_DATAGRID_NAME]: bookDatagridConfiguration,
    [constants.READERS_DATAGRID_NAME]: readerDatagridConfiguration,
};