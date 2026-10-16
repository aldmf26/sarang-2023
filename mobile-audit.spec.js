const { test } = require('@playwright/test');

test('audit invoice mobile', async ({ browser }) => {
  const context = await browser.newContext({
    viewport: { width: 390, height: 844 },
    deviceScaleFactor: 1,
    isMobile: true,
    hasTouch: true,
  });
  const page = await context.newPage();
  const logs = [];
  page.on('console', message => logs.push(`console:${message.type()}:${message.text()}`));
  page.on('pageerror', error => logs.push(`pageerror:${error.message}`));
  await page.goto('http://sarang-2023.test/login', { waitUntil: 'domcontentloaded' });
  await page.locator('#email').fill('aldi@gmail.com');
  await page.locator('#password').fill('password');
  await Promise.all([
    page.waitForLoadState('domcontentloaded'),
    page.locator('button[type="submit"]').click(),
  ]);
  await page.goto('http://sarang-2023.test/home/opname-menu/bulanan/invoice/5', { waitUntil: 'networkidle' });
  await page.screenshot({ path: 'outputs/invoice-5-mobile.png', fullPage: true });
  const metrics = await page.evaluate(() => {
    const interactive = [...document.querySelectorAll('a,button,input,select,textarea')].map((el) => {
      const r = el.getBoundingClientRect();
      const style = getComputedStyle(el);
      return {
        tag: el.tagName.toLowerCase(),
        text: (el.innerText || el.value || el.getAttribute('aria-label') || '').trim().replace(/\s+/g, ' ').slice(0, 80),
        type: el.getAttribute('type'),
        w: Math.round(r.width), h: Math.round(r.height), x: Math.round(r.x), y: Math.round(r.y),
        visible: r.width > 0 && r.height > 0 && style.visibility !== 'hidden' && style.display !== 'none',
      };
    }).filter(x => x.visible);
    const clipped = [...document.querySelectorAll('body *')].map((el) => {
      const r = el.getBoundingClientRect();
      return { tag: el.tagName.toLowerCase(), cls: String(el.className || '').slice(0, 100), x: Math.round(r.x), right: Math.round(r.right), w: Math.round(r.width) };
    }).filter(x => x.right > innerWidth + 1 || x.x < -1).slice(0, 50);
    return {
      url: location.href,
      title: document.title,
      viewport: { width: innerWidth, height: innerHeight },
      body: { scrollWidth: document.body.scrollWidth, clientWidth: document.documentElement.clientWidth, scrollHeight: document.body.scrollHeight },
      h1: [...document.querySelectorAll('h1,h2,h3,h4,h5')].map(e => e.innerText.trim()).filter(Boolean),
      text: document.body.innerText.replace(/\n{3,}/g, '\n\n').slice(0, 12000),
      interactive,
      smallTargets: interactive.filter(x => x.w < 44 || x.h < 44),
      clipped,
    };
  });
  console.log('AUDIT_JSON=' + JSON.stringify(metrics));
  console.log('BROWSER_LOGS=' + JSON.stringify(logs));
  await context.close();
});
