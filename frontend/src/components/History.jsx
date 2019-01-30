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
import MenuItem from '@material-ui/core/MenuItem';
import Select from '@material-ui/core/Select';
import InputLabel from '@material-ui/core/InputLabel';
import FormControl from '@material-ui/core/FormControl';
import $ from 'jquery';
import Button from '@material-ui/core/Button';

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
  bold: { fontWeight: 'bold' }
});


class CustomizedTable extends React.Component {
  constructor(props){
    super(props);
    this.state = { 
      balances: [],
      short_name: '',
      contract: '',
      ground: '',
      allRenters: [],
      allContracts: []
    }
    this.handleChange = this.handleChange.bind(this);
    this.filterBalances = this.filterBalances.bind(this);
  }
   
  componentDidMount() {
    this.fetchBalances();

    setTimeout(() => {
      const { balances } = this.state;
      balances.forEach(balance => balance.balance = parseFloat(balance.balance));
      this.setState({ balances: balances });
    }, 300);
  }

  fetchBalances = () => {
    fetch(`${window.location.hostname === 'localhost' ? 'http://arenda.local' : window.location.origin}/api/balance/read.php`)
      .then(response => response.json())
      .then(balances => {
        this.setState({ 
          balances, 
          allContracts: this.filterProp('contract', balances),
          allRenters: this.filterProp('short_name', balances) 
        });
      }) 
      .catch(err => console.log(err)) 
    
      setTimeout(() => {
        const { balances } = this.state;
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

  filterProp = (prop, array) => {
    const filtered = []
    for(let item of array) {
      if(!filtered.includes(item[prop])) {
        filtered.push(item[prop]);
      }
    }
    return filtered;
  }

  async handleChange(e) {
    await this.fetchBalances();
    setTimeout(() => this.filterBalances(e), 300); 
  }

  filterBalances = (e) => {
    this.setState({ [e.target.name]: e.target.value });
    const filtered = this.state.balances;
    let balances = filtered.filter(balance => {
      return balance[e.target.name] == e.target.value
    });

    this.setState({ balances });
  }

  removeFilters = () => {
    this.setState({short_name: '', contract: '', ground: '', });
    this.fetchBalances();
  }

  render() {
    const { classes } = this.props;
    const { balances, allContracts, allRenters, short_name, contract, renter } = this.state;

    return (
    <React.Fragment>  
      <Paper className={classes.root}>
        <Table className={classes.table}>
          <TableHead>
            <TableRow>
              <CustomTableCell style={{ padding: '0 20px' }}>
                <FormControl style={{ width:'100%' }}>
                  <InputLabel htmlFor="renter" style={{ color:'white' }}>Арендатор</InputLabel>
                  <Select
                    value={this.state.short_name}
                    onChange={this.handleChange}
                    inputProps={{
                      name: 'short_name',
                      id: 'renter',
                    }}
                  >
                  {allRenters.map((renter, index) => (
                    <MenuItem key={index} value={renter}>{ renter }</MenuItem>
                  ))}
                  </Select>
                </FormControl>
              </CustomTableCell>
              <CustomTableCell align="right" style={{ padding: '0 20px' }}>
                <FormControl style={{ width:'100%' }}>
                  <InputLabel htmlFor="contract" style={{ color:'white' }}>Договоры</InputLabel>
                  <Select
                    value={this.state.contract}
                    onChange={this.handleChange}
                    inputProps={{
                      name: 'contract',
                      id: 'contract',
                    }}
                  >
                  {allContracts.map((contract,index) => (
                    <MenuItem key={index} value={contract}>{ contract }</MenuItem>
                  ))}
                  </Select>
                </FormControl>
              </CustomTableCell>
              <CustomTableCell align="right" style={{ padding: '0 20px' }}>
                <FormControl style={{ width:'100%' }}>
                  <InputLabel htmlFor="action" style={{ color:'white' }}>Действие</InputLabel>
                  <Select
                    value={this.state.ground}
                    onChange={this.handleChange}
                    inputProps={{
                      name: 'ground',
                      id: 'action',
                    }}
                  >
                    <MenuItem value="счёт">Счёт</MenuItem>
                    <MenuItem value="оплата">Оплата</MenuItem>
                  </Select>
                </FormControl>
              </CustomTableCell>
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

      {renter || contract || short_name ?
      <Button variant="contained" onClick={this.removeFilters} className={ classes.button }>
        Сбросить фильтры
      </Button> : null}
    </React.Fragment>  
    );
  }
}

CustomizedTable.propTypes = {
  classes: PropTypes.object.isRequired,
};

export default withStyles(styles)(CustomizedTable);