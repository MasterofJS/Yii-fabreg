import React from 'react';
var Form = React.createClass({
    render: function () {
        return (
            <form action="" data-abide="ajax">
                <div className="row">
                    <input type="email" required placeholder="Enter your email address here..."/>
                    <small className="error">Invalid email</small>
                </div>
                <div className="row">
                    <button type="submit">subscribe</button>
                </div>
            </form>
        )
    }
});

module.exports = Form;

//





