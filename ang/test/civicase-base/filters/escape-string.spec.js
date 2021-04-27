describe('Civicase Escape String', () => {
  let civicaseEscapeString;

  beforeEach(module('civicase-base'));

  beforeEach(inject(($filter) => {
    civicaseEscapeString = $filter('civicaseEscapeString');
  }));

  describe('when escaping a string containing special HTML characters', () => {
    it('escapes the special characters', () => {
      expect(civicaseEscapeString('You & Me')).toBe('You &amp; Me');
    });
  });

  describe('when escaping strings that have previously been escaped', () => {
    it('leaves the string as is', () => {
      expect(civicaseEscapeString('You &amp; Me')).toBe('You &amp; Me');
    });
  });

  describe('whnen escaping partially escaped strings', () => {
    it('only escapes the HTML entities that have not already been escaped', () => {
      expect(civicaseEscapeString('You &amp; Me & Myself')).toBe('You &amp; Me &amp; Myself');
    });
  });
});
