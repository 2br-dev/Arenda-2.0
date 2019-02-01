import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import Paper from '@material-ui/core/Paper';

const CustomTableCell = withStyles(theme => ({
  head: {
    backgroundColor: theme.palette.common.black,
    color: theme.palette.common.white,
  },
  body: {
    fontSize: 16
  },
}))(TableCell);

const styles = theme => ({
  root: {
    width: '70%',
    marginTop: '50px',
    overflowX: 'auto',
    margin: '0 auto',
    maxWidth: '1000px'
  },
  table: {
    minWidth: 500,
  },
  row: {
    '&:nth-of-type(odd)': {
      backgroundColor: theme.palette.background.default,
    },
  },
  success: { color: 'green' },
  warning: { color: 'red' },
  bold: { fontWeight: 'bold' }
});


class RentersData extends React.Component {
  state = { renters: [] }

  componentDidMount = () => {
    this.fetchBalances();
  }

  fetchBalances = () => {
    fetch(`${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/renters/read.php`)
      .then(response => response.json())
      .then(renters => this.setState({ renters }))
      .catch(err => console.log(err))
  }

  render() {
    const { classes } = this.props;
    const { renters } = this.state;


    return (
      <Paper className={classes.root}>
        <Table className={classes.table}>
            <TableRow>
              <CustomTableCell>Арендатор</CustomTableCell>
              <CustomTableCell align="right">Логин</CustomTableCell>
              <CustomTableCell align="right">Пароль</CustomTableCell>
            </TableRow>
          <TableBody>
            {renters.map(renter => (
              <React.Fragment>
              <TableRow className={classes.row} key={renter.id}>
                <CustomTableCell component="th" scope="row" className={classes.bold}>
                  {renter.short_name}
                </CustomTableCell>
                <CustomTableCell align="right">{renter.login}</CustomTableCell>
                <CustomTableCell align="right">{renter.password}</CustomTableCell>
              </TableRow>
              <TableRow>
                <CustomTableCell>Арендатор</CustomTableCell>
                <CustomTableCell align="right">Логин</CustomTableCell>
                <CustomTableCell align="right">Пароль</CustomTableCell>
              </TableRow>
              </React.Fragment>
            ))}
          </TableBody>
        </Table>
      </Paper>
    );
  }
}

RentersData.propTypes = {
  classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(RentersData);