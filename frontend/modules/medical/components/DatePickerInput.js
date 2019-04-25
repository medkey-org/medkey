import React from 'react';
import PropTypes from 'prop-types';

class DatePickerInput extends React.Component {
    render () {
        return (
            <button
                className="btn btn-default btn-xs btn-calendar"
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