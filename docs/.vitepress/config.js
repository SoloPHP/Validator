import { defineConfig } from 'vitepress'

export default defineConfig({
  title: 'Solo Validator',
  description: 'Lightweight PHP validation library with custom rules and structured errors',
  base: '/Validator/',
  
  head: [
    ['link', { rel: 'icon', type: 'image/svg+xml', href: '/Validator/logo.svg' }],
    ['meta', { name: 'theme-color', content: '#8b5cf6' }],
    ['meta', { property: 'og:type', content: 'website' }],
    ['meta', { property: 'og:title', content: 'Solo Validator' }],
    ['meta', { property: 'og:description', content: 'Lightweight PHP validation library with custom rules and structured errors' }],
  ],

  themeConfig: {
    logo: '/logo.svg',
    
    nav: [
      { text: 'Guide', link: '/guide/installation' },
      { text: 'Rules', link: '/features/rules' },
      { text: 'API', link: '/api/validator' },
      { text: 'v3.0.0', link: 'https://github.com/solophp/validator/releases' },
      {
        text: 'Links',
        items: [
          { text: 'GitHub', link: 'https://github.com/solophp/validator' },
          { text: 'Packagist', link: 'https://packagist.org/packages/solophp/validator' },
          { text: 'SoloPHP', link: 'https://github.com/solophp' }
        ]
      }
    ],

    sidebar: [
      {
        text: 'Getting Started',
        items: [
          { text: 'Installation', link: '/guide/installation' },
          { text: 'Quick Start', link: '/guide/quick-start' }
        ]
      },
      {
        text: 'Features',
        items: [
          { text: 'Validation Rules', link: '/features/rules' },
          { text: 'Custom Rules', link: '/features/custom-rules' }
        ]
      },
      {
        text: 'API Reference',
        items: [
          { text: 'Validator', link: '/api/validator' }
        ]
      }
    ],

    socialLinks: [
      { icon: 'github', link: 'https://github.com/solophp/validator' }
    ],

    footer: {
      message: 'Released under the MIT License.',
      copyright: `Copyright © 2024-${new Date().getFullYear()} SoloPHP`
    },

    search: {
      provider: 'local'
    },

    editLink: {
      pattern: 'https://github.com/solophp/validator/edit/main/docs/:path',
      text: 'Edit this page on GitHub'
    }
  }
})
