import React from 'react';
import PropTypes from 'prop-types';
import { withStyles } from '@material-ui/core/styles';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableCell from '@material-ui/core/TableCell';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import Paper from '@material-ui/core/Paper';
import DeleteIcon from '@material-ui/icons/Delete';
import Tooltip from '@material-ui/core/Tooltip';
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
  success: { color: 'green' },
  warning: { color: 'red' },
  bold: { fontWeight: 'bold' }
});


class CustomizedTable extends React.Component {
  state = { balances: [] }
   
  componentDidMount = () => {
    this.fetchBalances();
  }

  fetchBalances = () => {
    fetch(`${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/balance/read.php`)
      .then(response => response.json())
      .then(balances => this.setState({ balances })) 
      .catch(err => console.log(err)) 
  }

  renameString = string => {
    switch(string) {
      case 'peni':
        return 'пени';
      case 'payment':
        return 'оплата';
      case 'schet':
        return 'счёт';
      case 'peni-payment':
        return 'оплата пени';
      default:
        return 'нет такого типа';
    }
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
    const { classes } = this.props;
    const { balances } = this.state;

    balances.forEach(balance => {
      balance.ground = this.renameString(balance.ground);
      balance.balance = parseFloat(balance.balance);
    });

    return (
      <Paper className={classes.root}>
        <Table className={classes.table}>
          <TableHead>
            <TableRow>
              <CustomTableCell>Арендатор</CustomTableCell>
              <CustomTableCell align="right">Договор</CustomTableCell>
              <CustomTableCell align="right">Действие</CustomTableCell>
              <CustomTableCell align="right">Сумма</CustomTableCell>
              <CustomTableCell align="right">Дата</CustomTableCell>
              <CustomTableCell align="right">Старт аренды</CustomTableCell>
              <CustomTableCell align="right">Текущий баланс</CustomTableCell>
              <CustomTableCell></CustomTableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {balances.map(balance => (
              <TableRow className={ classes.row } key={ balance.id }>
                <CustomTableCell component="th" scope="row" className={ classes.bold }>
                  { balance.short_name }
                </CustomTableCell>
                <CustomTableCell align="right">{ balance.contract }</CustomTableCell>
                <CustomTableCell align="right">{ balance.ground }</CustomTableCell>
                <CustomTableCell align="right">{ Number(balance.summa).toFixed(2) } ₽ </CustomTableCell>
                <CustomTableCell align="right">{ balance.date.split('-').reverse().join('.') }</CustomTableCell>
                <CustomTableCell align="right">{ balance.start_arenda }</CustomTableCell>
                <CustomTableCell 
                  align="right" 
                  className={ balance.balance < 0 ? classes.warning : classes.success }
                  >
                  { Number(balance.balance).toFixed(2) } ₽
                </CustomTableCell>
                {balance.ground === 'счёт' ? (
                  <Tooltip disableFocusListener placement="right" disableTouchListener title="Удалить">
                    <CustomTableCell 
                      className={ classes.delete } 
                      onClick={() => {
                        this.deleteBalanceRecord(balance.id, balance.short_name, balance.contract, balance.ground, balance.ground_id, balance.summa)
                      }}>
                      <DeleteIcon />
                    </CustomTableCell>  
                  </Tooltip>  
                ) : 
                (
                  <CustomTableCell></CustomTableCell>
                )}
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Paper>
    );
  }
}

CustomizedTable.propTypes = {
  classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(CustomizedTable);