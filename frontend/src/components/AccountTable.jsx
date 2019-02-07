import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import Paper from '@material-ui/core/Paper';
import $ from 'jquery';

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
    margin: '0 auto'
  },
  table: {
    minWidth: 700,
  },
  row: {
    '&:nth-of-type(odd)': {
      backgroundColor: theme.palette.background.default,
    },
  },
  delete: {
    '&:hover': {
      cursor: 'pointer',
      background: '#ffebee',
      transition: '.37s ease'
    }
  },
  button: {
    background: '#000 !important',
    color: '#fff !important',
    width: '300px',
    display: 'flex',
    margin: '30px auto',
    '&:hover': {
      background: '#232323'
    }
  },
  success: { color: 'green' },
  warning: { color: 'red' },
  bold: { fontWeight: 'bold' },
  payment: {
    background: '#e8f5e9'
  }
});


class AccountTable extends React.Component { 
  componentDidMount() {
    setTimeout(() => {
      const { balances } = this.props;
      balances.forEach(balance => balance.balance = parseFloat(balance.balance));
      this.setState({ balances: balances });
    }, 300);
  }

  deleteBalanceRecord = (id, renter, number, ground, ground_id, summa) => {
    const self = this;
    $.ajax({
      type: "POST",
      url: `${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/balance/delete.php`,
      data: { id, renter, number, ground, ground_id, summa },
      success: function(res){
        console.log(res);
        self.fetchBalances();
      },
      error: function(err) {
        console.log(err);
      }
    });   
  }

  render() {
    const { classes, balances } = this.props;

    return (
    <React.Fragment>  
      <Paper className={classes.root}>
        <Table className={classes.table}>
          <TableHead>
            <TableRow>
              <CustomTableCell align="right" style={{ padding: '0 20px' }}>Договоры</CustomTableCell>
              <CustomTableCell align="right" style={{ padding: '0 20px' }}>Действие</CustomTableCell>
              <CustomTableCell align="right">Сумма</CustomTableCell>
              <CustomTableCell align="right">Остаток</CustomTableCell>
              <CustomTableCell align="right">Дата</CustomTableCell>
              <CustomTableCell align="right">Текущий баланс</CustomTableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {balances.map(balance => (
              <TableRow className={ balance.ground === 'оплата' ? classes.payment : classes.row } key={ balance.id }>
                <CustomTableCell align="right">{ balance.contract }</CustomTableCell>
                <CustomTableCell align="right">{ balance.ground }</CustomTableCell>
                <CustomTableCell align="right">{ Number(balance.summa.replace(/,/g, '.')).toFixed(2) } ₽ </CustomTableCell>
                <CustomTableCell align="right">{ balance.rest ? `${balance.rest} ₽` : '' }  </CustomTableCell>
                <CustomTableCell align="right">{ balance.date }</CustomTableCell>
                <CustomTableCell 
                  align="right" 
                  className={ balance.balance < 0 ? classes.warning : classes.success }
                  >
                  { Number(balance.balance).toFixed(2) } ₽
                </CustomTableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Paper>
    </React.Fragment>  
    );
  }
}

AccountTable.propTypes = {
  classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(AccountTable);