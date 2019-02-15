import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import AppBar from '@material-ui/core/AppBar';
import Tabs from '@material-ui/core/Tabs';
import NoSsr from '@material-ui/core/NoSsr';
import Tab from '@material-ui/core/Tab';
import Paper from '@material-ui/core/Paper';
import InputContainer from './stats/InputContainer';
import TabContainer from './stats/TabContainer';

function LinkTab(props) {
  return <Tab component="a" onClick={event => event.preventDefault()} {...props} />;
}

const styles = theme => ({
  root: {
    flexGrow: 1,
    width: '70%',
    margin: '50px auto',
    backgroundColor: theme.palette.background.paper
  },
  link: {
    '&:hover': {
      textDecoration: 'none'
    }
  }
});

class Stats extends React.Component {
  state = {
    value: 0,
  };

  handleChange = (event, value) => {
    this.setState({ value });
  };

  render() {
    const { classes } = this.props;
    const { value } = this.state;

    return (
      <NoSsr>
        <div className={classes.root}>
          <AppBar position="static" style={{ background: '#232323' }}>
            <Tabs variant="fullWidth" value={value} onChange={this.handleChange}>
              <LinkTab label="Добавить" href="page1" className={classes.link} />
              <LinkTab label="Сводка" href="page2" className={classes.link} />
            </Tabs>
          </AppBar>
          <Paper>
            {value === 1 ? <TabContainer></TabContainer> : <InputContainer></InputContainer>}
          </Paper>
        </div>
      </NoSsr>
    );
  }
}

Stats.propTypes = {
  classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(Stats);