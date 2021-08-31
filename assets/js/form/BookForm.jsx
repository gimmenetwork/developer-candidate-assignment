import React, {useState} from 'react';
import {useDispatch} from "react-redux";
import Backdrop from '@material-ui/core/Backdrop';
import CircularProgress from '@material-ui/core/CircularProgress';
import loadStyle from "App/style";
import {makeStyles} from "@material-ui/core/styles";
import {useFormik} from "formik";
import * as yup from 'yup';
import Button from '@material-ui/core/Button';
import TextField from "@material-ui/core/TextField";
import Select from '@material-ui/core/Select';
import MenuItem from '@material-ui/core/MenuItem';
import FormControl from '@material-ui/core/FormControl';
import {getItemUrlForDatagrid, getUrlForDatagrid, route} from "App/utils";
import constants from "App/constants";
import { get, isNil} from "lodash";
import {createItem, updateItem, getReaders} from "App/actions/actions";
import {FormHelperText, Grid} from "@material-ui/core";


const useStyles = makeStyles((theme) => (loadStyle(theme)));

const BookForm = ({onSuccessCallback, onFailCallback, data = null, genres = []}) => {
    const classes = useStyles();
    const dispatch = useDispatch();

    if (!isNil(data)) {
        data = Object.assign(data, {genre: get(data, 'genre.id')});
    }

    const itemUrl = getItemUrlForDatagrid(constants.BOOKS_DATAGRID_NAME);
    const collectionUrl = getUrlForDatagrid(constants.BOOKS_DATAGRID_NAME);

    const [values, setValues] = useState(data);
    const [loading, setLoading] = useState(false);

    const validationSchema = yup.object({
        name: yup
            .string('Enter book name')
            .required('Name is required'),
        author: yup
            .string('Enter author name')
            .required('Author is required'),
        genre: yup
            .string('Select genre')
            .required('Genre is required'),
    });

    const submit = values => {
        const id = get(data, 'id');
        let url = collectionUrl;

        setLoading(true);
        setValues(values);
        if (!isNil(id)) {
            // update item
            url = route(itemUrl, {':id': id});
            dispatch(updateItem(url, values))
                .then(response => {
                    setLoading(false);
                    onSuccessCallback(values);
                })
                .catch(error => {
                    setLoading(false);
                    onFailCallback(error);
                });
        } else {
            // create item
            dispatch(createItem(url, values))
                .then(response => {
                    setLoading(false);
                    onSuccessCallback(values);
                })
                .catch(error => {
                    setLoading(false);
                    onFailCallback(error);
                });
        }
    }

    const WithMaterialUI = () => {
        const formik = useFormik({
            initialValues: {
                name: get(values, 'name', ''),
                author: get(values, 'author', ''),
                genre: get(values, 'genre', ''),
            },
            validationSchema: validationSchema,
            onSubmit: (values) => {
                submit(values);
            },
        });

        return (
            <div className={classes.formContainer}>
                <form onSubmit={formik.handleSubmit}>
                    <Grid container>
                        <Grid item xs={12}>
                    <FormControl className={classes.formControl}>
                    <TextField
                        fullWidth
                        id="name"
                        name="name"
                        label="Name"
                        value={formik.values.name}
                        onChange={formik.handleChange}
                        error={formik.touched.name && Boolean(formik.errors.name)}
                        helperText={formik.touched.name && formik.errors.name}
                    />
                    </FormControl>
                        </Grid>
                        <Grid item xs={12}>
                    <FormControl className={classes.formControl}>
                    <TextField
                        fullWidth
                        id="author"
                        name="author"
                        label="Author"
                        value={formik.values.author}
                        onChange={formik.handleChange}
                        error={formik.touched.author && Boolean(formik.errors.author)}
                        helperText={formik.touched.author && formik.errors.author}
                    />
                    </FormControl>
                        </Grid>
                        <Grid item xs={12}>
                        <FormControl className={classes.formControl}>
                            <Select
                                label={"Genre"}
                                id="select"
                                value={formik.values.genre || ''}
                                onChange={(opt, e) => {
                                    formik.setFieldValue('genre', opt.target.value || '');
                                }}
                                error={formik.touched.genre && Boolean(formik.errors.genre)}
                            >
                                <MenuItem value="">
                                    <em>Select genre</em>
                                </MenuItem>
                                {genres.map((item, key) => (
                                    <MenuItem key={key} value={get(item, 'id')}>
                                        {get(item, 'name')}
                                    </MenuItem>
                                ))}
                                helperText={formik.touched.genre && formik.errors.genre}
                            </Select>
                            {/*<FormHelperText error={(formik.touched.genre && formik.errors.genre)}>{formik.errors.genre}</FormHelperText>*/}
                        </FormControl>
                        </Grid>
                        <Grid item xs={12}>
                            <Button color="primary" variant="contained" fullWidth type="submit">
                                Submit
                            </Button>
                        </Grid>
                    </Grid>
                </form>
                <Backdrop className={classes.backdrop} open={loading} onClick={() => {}}>
                    <CircularProgress color="inherit" />
                </Backdrop>
            </div>
        );
    };

    return <WithMaterialUI />;
}

export default BookForm;