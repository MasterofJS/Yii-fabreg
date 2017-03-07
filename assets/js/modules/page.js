import Aside from '../components/aside.jsx';

export default React.createClass({
    render () {
        var className = classNames('row', 'wrap_flex_', this.props.classes);
        return (
            <div className={className}>
                <section className="medium-8 column">
                    {this.props.children}
                </section>
                <Aside/>
            </div>
        );
    }
});
