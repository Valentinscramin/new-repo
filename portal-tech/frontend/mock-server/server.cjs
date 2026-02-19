const express = require('express')
const cors = require('cors')
const app = express()
const port = process.env.PORT || 5175

app.use(cors())
app.use(express.json())

app.get('/api/home', (req, res) => {
  res.json({
    sampleProducts: [
      { title: 'Produto A', rating: '4.8', image: '/img/monitor-1.svg', badge: 'Melhor Escolha 2026' },
      { title: 'Produto B', rating: '4.6', image: '/img/monitor-2.svg' },
      { title: 'Produto C', rating: '4.5', image: '/img/monitor-3.svg' }
    ],
    comparisonRows: [
      { feature: 'Taxa de Atualização', a: '240Hz', b: '165Hz', c: '144Hz' },
      { feature: 'Painel', a: 'IPS', b: 'VA', c: 'IPS' },
      { feature: 'Preço', a: '€899', b: '€799', c: '€749' },
      { feature: 'Avaliação', a: '4.8', b: '4.6', c: '4.5' }
    ],
    rankingItems: [
      { title: 'Produto A', subtitle: 'Monitor top para jogos', rating: '4.8' },
      { title: 'Produto B', subtitle: 'Bom custo-benefício', rating: '4.6' },
      { title: 'Produto C', subtitle: 'Ótima opção econômica', rating: '4.5' }
    ]
  })
})

app.listen(port, () => console.log(`Mock API server listening on http://localhost:${port}`))
