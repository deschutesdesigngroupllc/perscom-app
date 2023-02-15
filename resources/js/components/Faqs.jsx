import React from 'react'

import { Container } from './/Container'
import backgroundImage from '../../images/background-faqs.jpg'

const faqs = [
  [
    {
      question: 'Can I try PERSCOM.io before I purchase a subscription?',
      answer:
        'Absolutely. Visit the registration page and sign up for an account. You will have a 7-day feel trial before you need to purchase a subscription.'
    },
    {
      question: 'What payment methods do you support?',
      answer: 'We use Stripe as our payment gateway and support all major payment methods.'
    }
  ],
  [
    {
      question: 'I have a question. How do I get support?',
      answer: 'First, visit our documentation page and if you still have a question, visit the support section in our docs.'
    },
    {
      question: 'Why should I purchase a PERSCOM.io subscription?',
      answer:
        "We understand the complexity of managing personnel. We've taken this into account and offer all the services you need in one product."
    }
  ],
  [
    {
      question: 'Can I integrate my data with any other services?',
      answer:
        'Yes, you can. We provide a powerful API as well as other standardized authentication methods to integrate your data. We also offer widgets to display your data on an external website.'
    },
    {
      question: 'Can I change the URL of my account Dashboard?',
      answer: 'Yes. You must subscribe to the Pro plan and you can change it to whatever you would like.'
    }
  ]
]

export function Faqs() {
  return (
    <section id='faq' aria-labelledby='faq-title' className='relative overflow-hidden bg-gray-50 py-20 sm:py-32'>
      <h2 id='faq-title' className='sr-only'>
        Frequently asked questions.
      </h2>
      <div className='absolute top-0 left-1/2 -trangray-x-[30%] -trangray-y-[25%]'>
        <img src={backgroundImage} alt='' width={1558} height={946} />
      </div>
      <Container className='relative'>
        <div className='mx-auto max-w-2xl lg:mx-0'>
          <p className='font-display text-3xl font-bold tracking-tight text-gray-800 sm:text-4xl'>Frequently asked questions</p>
          <p className='mt-4 text-base text-gray-600'>
            If you can’t find what you’re looking for, visit our{' '}
            <a href='https://community.deschutesdesigngroup.com' target='_blank' rel='noreferrer'>
              community forums
            </a>{' '}
            or{' '}
            <a href='https://docs.perscom.io' target='_blank' rel='noreferrer'>
              documentation for more reference material
            </a>
            .
          </p>
        </div>
        <ul className='mx-auto mt-16 grid max-w-2xl grid-cols-1 gap-8 lg:max-w-none lg:grid-cols-3'>
          {faqs.map((column, columnIndex) => (
            <li key={columnIndex}>
              <ul className='space-y-8'>
                {column.map((faq, faqIndex) => (
                  <li key={faqIndex}>
                    <h3 className='font-display text-base font-semibold text-gray-700'>{faq.question}</h3>
                    <p className='mt-4 text-sm text-gray-600'>{faq.answer}</p>
                  </li>
                ))}
              </ul>
            </li>
          ))}
        </ul>
      </Container>
    </section>
  )
}
