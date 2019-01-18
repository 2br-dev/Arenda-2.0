export default function arenda(state = [], action) {
  switch (action.type) {
    case 'FETCH_RENTERS':
      return [...action.payload]
    default:
      return state
  }
}