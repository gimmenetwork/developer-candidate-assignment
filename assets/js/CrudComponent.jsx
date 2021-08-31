import React, { useState, useEffect } from 'react';
import {useSelector, useDispatch} from "react-redux";
import { DataGrid, GridRowsProp, GridColDef } from '@material-ui/data-grid';
import {
    getColumnsForDatagrid, getDataForDatagrid,
    getItemUrlForDatagrid, getUrlForDatagrid,
    route, getFormForDatagrid, getCustomActions,
    getCustomActionFormByActionName, getFilterMappingForDatagrid
} from "App/utils";
import {Backdrop, Button, Grid, Modal} from "@material-ui/core";
import loadStyle from "App/style";
import {makeStyles} from "@material-ui/core/styles";
import {get, isEmpty} from "lodash";
import {deleteItem, getItems, getItem, getGenres, clearItem} from "App/actions/actions";
import constants from "App/constants";
import FilterManager from "App/Helper/FilterManager";

const useStyles = makeStyles((theme) => (loadStyle(theme)));

const CrudComponent = ({name}) => {
    const classes = useStyles();

    const dispatch = useDispatch();

    // get the datagrid columns defines in datagrid definition
    const columns = getColumnsForDatagrid(name);

    // get the datagrid filterMapping data defined in datagrid definition
    const filterMapping = getFilterMappingForDatagrid(name);

    // get the collection url defined in datagrid definition
    let url = getUrlForDatagrid(name);
    // get the item url defined in datagrid definition
    const itemUrl = getItemUrlForDatagrid(name);
    // get the form defined in datagrid definition
    const Form = getFormForDatagrid(name);

    const genres = useSelector(state => {
        return get(state, [name, 'genres']);
    });
    const forceReload = useSelector(state => get(state, [name, 'forceReload']));
    const rows = useSelector(state => {
        const items = get(state, [name, 'items']);
        return getDataForDatagrid(name, items)
    });
    const totalItems = useSelector(state => get(state, [name, 'count']));
    const loading = useSelector(state => get(state, [name, 'collectionLoading']));
    const item = useSelector(state => get(state, [name, 'item']));


    const [actionType, setActionType] = useState('edit');
    const [modalOpen, setModalOpen] = useState(false);
    const [customActionModalOpen, setCustomActionModalOpen] = useState(false);
    const [page, setPage] = useState(1);
    const [filterQuery, setFilterQuery] = useState('');
    const [reload, setReload] = useState(null);
    const [itemData, setItemData] = useState({});

    let CustomActionForm = getCustomActionFormByActionName(name, actionType);

    // handles genres collection
    useEffect(() => {
        dispatch(getGenres(constants.GENRE_API_COLLECTION_URL));
    }, []);

    // handles the collection request
    useEffect(() => {
        if (reload !== false || forceReload) {
            url = url + '?page=' + page;

            if (filterQuery) {
                url = url + '&' + filterQuery;
            }

            dispatch(getItems(url, name));
            setReload(false);
        }
    }, [reload, forceReload]);

    // handle the edit item event
    useEffect(() => {
        if (item) {
            setItemData(item);
            if (actionType == 'edit') {
                setModalOpen(true);
            } else {
                setCustomActionModalOpen(true);
            }
        } else {
            setItemData({});
        }
    }, [item]);

    // handles the page change event
    const onPageChange = page => {
        setPage(parseInt(page) +1 );
        setReload(true);
    }

    // handles the filter side
    const handleFilterModelChange = model => {
        const filterManager = new FilterManager(model, filterMapping);
        const fq = filterManager.getFilterQuery();
        const query = fq.getQuery();

        if (filterQuery !== query) {
            setFilterQuery(query);
            setReload(true);
        }

    }

    // handles the delete button
    const onDelete = id => {
        const url = getItemUrlForDatagrid(name);
        dispatch(deleteItem(route(url, {':id': id}), name));
    }

    // handles the edit button
    const onEdit = id => {
        setActionType('edit');
        dispatch(getItem(route(itemUrl, {':id': id}), name));
    }

    const onCustomAction = (id, customAction) => {
        setActionType(customAction);
        dispatch(getItem(route(itemUrl, {':id': id}), name));
    }

    const onSubmitSuccess = data => {
        if (actionType == 'edit') {
            setModalOpen(false);
        } else {
            setCustomActionModalOpen(false);
        }

        dispatch(clearItem(name));
        setFilterQuery('');
        setReload(true);
    }

    const onSubmitFail = data => {
        if (actionType == 'edit') {
            setModalOpen(false);
        } else {
            setCustomActionModalOpen(false);
        }

        dispatch(clearItem(name));
    }

    const handleClose = () => {
        if (actionType == 'edit') {
            setModalOpen(false);
        } else {
            setActionType('edit');
            setCustomActionModalOpen(false);
        }

        dispatch(clearItem(name));
    };

    // add actions
    columns.push({
        field: "",
        headerName: '',
        sortable: false,
        disableSelectionOnClick: true,
        disableColumnResize: true,
        disableColumnSelector: true,
        disableColumnFilter: true,
        disableColumnMenu: true,
        disableColumnReorder: true,
        loading: loading,
        width: 200,
        renderCell: params => {
            const loading = get(params, 'api.state.options.loading');
            const customActions = getCustomActions(name);
            return <>
            <Button color={"primary"} disabled={loading} onClick={() => onDelete(params.id) }>Delete</Button>
            <Button color={"primary"} disabled={loading} onClick={() => onEdit(params.id) }>Edit</Button>
                {(customActions.map(customAction => {
                    return <Button
                        key={get(customAction, 'name')}
                        color={"primary"}
                        disabled={loading}
                        onClick={() => onCustomAction(params.id, get(customAction, 'name'))}
                    >
                        {get(customAction, 'name')}
                    </Button>
                }))}
                </>;
        }

    });

    return <>
            <Grid container item xs={12} direction={"row"} justifyContent={"flex-end"} className={classes.container2}>
                <Button disabled={isEmpty(genres)} variant="contained" color="primary" onClick={() => setModalOpen(true)}>Add</Button>
            </Grid>
            <Grid container item xs={12}>
                <div style={{ height: 650, width: '100%' }}>
                    <DataGrid
                        loading={loading}
                        rows={rows}
                        columns={columns}
                        paginationMode="server"
                        rowsPerPageOptions={[10]}
                        pageSize={10}
                        rowCount={totalItems}
                        disableSelectionOnClick={true}
                        onPageChange={page => onPageChange(page)}
                        filterMode="server"
                        onFilterModelChange={handleFilterModelChange}
                        //disableColumnFilter={true}
                        disableColumnSelector={true}
                        sort
                    />
                </div>
            </Grid>
        <Modal
            aria-labelledby="title"
            aria-describedby="description"
            className={classes.modal}
            open={itemData && modalOpen}
            onClose={handleClose}
            closeAfterTransition
            BackdropComponent={Backdrop}
            BackdropProps={{
                timeout: 500,
            }}
        >
            <div className={classes.paperModal}>
                {Form && <Form
                    data={itemData} onSuccessCallback={onSubmitSuccess} onFailCallback={onSubmitFail} genres={genres}
                />}
            </div>
        </Modal>
        <Modal
            aria-labelledby="custom-action-title"
            aria-describedby="custom-action-description"
            className={classes.modal}
            open={customActionModalOpen}
            onClose={handleClose}
            closeAfterTransition
            BackdropComponent={Backdrop}
            BackdropProps={{
                timeout: 500,
            }}
        >
            <div className={classes.paperModal}>
                {typeof CustomActionForm === 'function' && itemData && <CustomActionForm
                    data={itemData} onSuccessCallback={onSubmitSuccess} onFailCallback={onSubmitFail}
                />}
            </div>
        </Modal>
        </>;
}

export default CrudComponent;
