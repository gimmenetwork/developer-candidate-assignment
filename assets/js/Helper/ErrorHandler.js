import {get, has, replace, isEmpty} from "lodash";

class ErrorHandler
{
    constructor(errors = []) {
        this.errors = errors;
        this.hasFormikErrors = false;
        this.hasFormErrors = false;
    }

    hasFormikErrors = () => {
        return this.hasFormikErrors;
    }

    hasFormErrors = () => {
        return this.hasFormErrors;
    }

    /**
     * Get errors in specific format to be able to be used by formik errors handling process
     *
     * @return {{}}
     */
    getFormikErrors = () => {
        let formikErrors = {};
        (this.errors || []).map(error => {
            if (has(error, 'cause')) {
                const message = get(error, ['cause', 'message']);
                const property = replace(get(error, ['cause', 'propertyPath'], ''), 'data\.', '');

                formikErrors = Object.assign(formikErrors, {
                    [property]: message,
                });
            }
        });

        this.hasFormikErrors = !isEmpty(formikErrors);

        return formikErrors;
    }

    /**
     * Get errors as array to be able to be used by the form
     *
     * @return {[]}
     */
    getFormErrors = () => {
        let formErrors = [];
        (this.errors || []).map(error => {
            if (!has(error, 'cause')) {
                const message = get(error, ['message']);
                formErrors.push(message);
            }
        });

        this.hasFormErrors = !isEmpty(formErrors);

        return formErrors;
    }
}

export default ErrorHandler;