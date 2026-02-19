export async function getHomeData() {
  const endpoint = 'http://localhost:5175/api/home'
  try {
    const res = await fetch(endpoint, { cache: 'no-store' })
    if (!res.ok) throw new Error('Network response was not ok')
    return await res.json()
  } catch (err) {
    // fallback to local data if mock server isn't running
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
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
          ],
          bestPrices: [
            { marketplace: 'Amazon', price: '€879', link: '#' },
            { marketplace: 'Worten', price: '€899', link: '#' },
            { marketplace: 'AliExpress', price: '€845', link: '#' }
          ]
        })
      }, 120)
    })
  }
}
