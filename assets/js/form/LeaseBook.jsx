import React, {useState, useEffect} from 'react';
import {useSelector, useDispatch} from "react-redux";
import Backdrop from '@material-ui/core/Backdrop';
import CircularProgress from '@material-ui/core/CircularProgress';
import loadStyle from "App/style";
import {makeStyles} from "@material-ui/core/styles";
import {useFormik} from "formik";
import * as yup from 'yup';
import Button from '@material-ui/core/Button';
import TextField from "@material-ui/core/TextField";
import Autocomplete from '@material-ui/lab/Autocomplete';
import FormControl from '@material-ui/core/FormControl';
import {getFormattedErrors, getTouchedFieldsByErrors, route} from "App/utils";
import constants from "App/constants";
import { get, isEmpty, find } from "lodash";
import {getReaders, patchItem} from "App/actions/actions";
import {FormHelperText, Grid} from "@material-ui/core";
import ErrorHandler from "App/Helper/ErrorHandler";

const useStyles = makeStyles((theme) => (loadStyle(theme)));

const LeaseBook = ({onSuccessCallback, data = null}) => {
    const classes = useStyles();
    const dispatch = useDispatch();

    const readers = useSelector(state => {return get(state, ['readers', 'readers'])});
    const collectionLoading = useSelector(state => get(state, ['readers', 'collectionLoading']));

    const [values, setValues] = useState(data);
    const [loading, setLoading] = useState(false);
    const [errors, setErrors] = useState({formikErrors: {}, formErrors: []});
    // const [formikErrors, setFormikErrors] = useState({});
    // const [formErrors, setFormErrors] = useState([]);
    const [touchedFields, setTouchedFields] = useState({});

    const validationSchema = yup.object({
        reader: yup
            .string('Select reader')
            .required('Reader is required'),
        returnDate: yup
            .string('Select return date')
            .required('Return date is required'),
    });

    useEffect(() => {
        dispatch(getReaders(constants.READERS_API_COLLECTION_URL, 'readers'));
    }, []);

    const submit = values => {
        const id = get(data, 'id');
        let url = route(constants.LEASE_BOOK_API_URL, {':id': id});
        setErrors({
            formikErrors: {},
            formErrors: [],
        });
        setTouchedFields({});
        setLoading(true);
        setValues(values);

        dispatch(patchItem(url, values))
            .then(response => {
                setLoading(false);
                onSuccessCallback(values);
            })
            .catch(error => {
                const errorHandler = new ErrorHandler(get(error, ['response', 'data']));
                const formikErrors = errorHandler.getFormikErrors();
                const touchedFields = getTouchedFieldsByErrors(formikErrors);
                const formErrors = errorHandler.getFormErrors();
                setErrors({
                    formikErrors: formikErrors,
                    formErrors: formErrors,
                });
                setTouchedFields(touchedFields);
                setLoading(false);
            });

    }

    const WithMaterialUI = () => {
        const {formikErrors, formErrors} = errors;
        const formik = useFormik({
            initialValues: {
                reader: get(values, 'reader', ''),
                returnDate: get(values, 'returnDate', ''),
            },
            initialErrors: formikErrors,
            initialTouched: touchedFields,
            validationSchema: validationSchema,
            onSubmit: (values) => {
                submit(values);
            },
        });

        let selectedValue = '';
        const defaultValue = {id: '', name: 'Select a reader'};
        if (isEmpty(find(readers, defaultValue))) {
            readers.unshift(defaultValue);
        }

        (readers || []).map(item => {
            if (get(item, 'id') === formik.values.reader) {
                selectedValue = item;
            }
        });


        return (
            <div className={classes.formContainer}>
                <form onSubmit={formik.handleSubmit}>
                    <Grid container>
                        <Grid item xs={12}>
                            <p>Lease book {get(data, 'name')}</p>
                        </Grid>
                        <Grid item xs={12}>
                            {
                                formErrors.map((error, key) => {
                                    return <FormHelperText key={key} error={true}>{error}</FormHelperText>;
                                })
                            }

                        </Grid>
                        {!collectionLoading && <>
                            <Grid item xs={12}>
                                <FormControl className={classes.formControl}>
                                    <Autocomplete
                                        id="reader"
                                        name="reader"
                                        options={readers || []}
                                        getOptionLabel={option => get(option, 'name', '')}
                                        value={selectedValue}
                                        getOptionSelected={(option, value) => {
                                            return (get(option, 'id') === get(value, 'id'));
                                        }}
                                        onChange={(e, value) => {
                                            formik.setFieldValue(
                                                "reader",
                                                get(value, 'id')
                                            );
                                        }}
                                        renderInput={params => (
                                            <TextField
                                                margin="normal"
                                                label="Reader"
                                                fullWidth
                                                name="reader"
                                                {...params}
                                                error={formik.touched.reader && Boolean(formik.errors.reader)}
                                                helperText={formik.errors.reader}
                                            />
                                        )}
                                    />
                                </FormControl>
                            </Grid>
                            <Grid item xs={12}>
                                <TextField
                                    id="date"
                                    label="Return date"
                                    fullWidth
                                    type="date"
                                    value={get(formik.values, 'returnDate', '')}
                                    onChange={event => formik.setFieldValue('returnDate', event.target.value)}
                                    InputLabelProps={{
                                        shrink: true,
                                    }}
                                    error={Boolean(formik.errors.returnDate)}
                                    helperText={formik.errors.returnDate}
                                />
                            </Grid>
                            <Grid item xs={12}>
                                <Button color="primary" variant="contained" fullWidth type="submit">
                                    Submit
                                </Button>
                            </Grid>
                        </>}
                    </Grid>
                </form>
                <Backdrop className={classes.backdrop} open={loading || collectionLoading} onClick={() => {}}>
                    <CircularProgress color="inherit" />
                </Backdrop>
            </div>
        );
    };

    return <WithMaterialUI />;
}

export default LeaseBook;