import React from 'react';
import PropTypes from 'prop-types';

class DatePickerInput extends React.Component {
    render () {
        return (
            <button
                className="example-custom-input btn btn-default"
                onClick={this.props.onClick}>
                {this.props.value}
            </button>
        )
    }
}

DatePickerInput.propTypes = {
    onClick: PropTypes.func,
    value: PropTypes.string
};

export default DatePickerInput;