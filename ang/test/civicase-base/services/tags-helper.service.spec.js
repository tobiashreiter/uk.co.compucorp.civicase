/* eslint-env jasmine */

(() => {
  describe('Tags Helper Service', () => {
    let TagsHelper, TagsMockData;

    beforeEach(module('civicase', 'civicase.data'));

    beforeEach(inject((_TagsHelper_, _TagsMockData_) => {
      TagsHelper = _TagsHelper_;
      TagsMockData = _TagsMockData_.get();
    }));

    describe('when displaying tags', () => {
      let tagMarkup;

      beforeEach(() => {
        tagMarkup = TagsHelper.formatTags({
          text: 'Tag Name',
          indentationLevel: '1'
        });
      });

      it('returns all the case statuses', () => {
        expect(tagMarkup).toEqual('<span style="margin-left:4px">Tag Name</span>');
      });
    });

    describe('when displaying generic tags', () => {
      let preparedTags, tags;

      beforeEach(() => {
        tags = [TagsMockData[0], TagsMockData[18]];
        preparedTags = TagsHelper.prepareGenericTags(tags);
      });

      it('shows the tags hierarchy', () => {
        expect(preparedTags).toEqual([{
          id: '1',
          name: 'Non-profit',
          text: 'Non-profit',
          description: 'Any not-for-profit organization.',
          is_selectable: '1',
          is_reserved: '0',
          is_tagset: '0',
          used_for: 'civicrm_contact',
          indentationLevel: 0
        }, {
          id: '18',
          name: 'L3',
          text: 'L3',
          parent_id: '1',
          is_selectable: '1',
          is_reserved: '0',
          is_tagset: '0',
          used_for: 'civicrm_activity',
          created_id: '202',
          created_date: '2018-12-27 07:38:15',
          indentationLevel: 1
        }]);
      });
    });

    describe('when displaying tagsets', () => {
      let preparedTags, tags;

      beforeEach(() => {
        tags = [TagsMockData[5], TagsMockData[6]];
        preparedTags = TagsHelper.prepareTagSetsTree(tags);
      });

      it('shows the tagsets and child tags', () => {
        expect(preparedTags).toEqual([{
          id: '6',
          name: 'Fruit',
          description: 'Sweet and nutritious',
          is_selectable: '1',
          is_reserved: '0',
          is_tagset: '1',
          used_for: 'civicrm_activity,civicrm_case',
          created_id: '202',
          created_date: '2018-10-11 12:38:07',
          children: [{
            id: '7',
            name: 'Apple',
            text: 'Apple',
            description: 'An apple a day keeps the Windows away',
            parent_id: '6',
            is_selectable: '1',
            is_reserved: '0',
            is_tagset: '0',
            used_for: 'civicrm_activity,civicrm_case',
            created_id: '202',
            color: '#ec3737',
            created_date: '2018-10-11 12:38:07'
          }]
        }]);
      });
    });
  });
})();
