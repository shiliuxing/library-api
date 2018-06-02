module.exports = {
  title: '在线借书平台',
  description: '一个连接读者与读书馆的图书资源共享平台',
  head: [
    ['link', { rel: 'icon', href: `/favicon.ico` }]
  ],
  base: '/docs/',
  themeConfig: {
    nav: [
      { text: '文档', link: '/guide/' },
      { text: '说明书',
        items: [
          { text: '需求分析说明书', link: '/specification/ras' },
          { text: '概要设计说明书', link: '/specification/ads' }
        ]
      }
    ],
    sidebar: {
      '/guide/': [{
        title: '文档',
        collapsable: false,
        children: [
          '',
          'feature',
          'install',
          'api',
          'back',
          'front',
          'config'
        ]
      }],
      '/specification/': [{
        title: '说明书',
        collapsable: false,
        children: [
          'ras',
          'ads'
        ]
      }]
    },
    repo: 'imageslr/library-api',
    docsDir: 'docs',
    editLinks: true,
    editLinkText: '在 Github 上编辑此页',
    lastUpdated: '上次更新'
  }
}
