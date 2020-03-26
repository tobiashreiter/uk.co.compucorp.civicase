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
      let preparedTags, tags, excpectedTags;;

      beforeEach(() => {
        tags = [TagsMockData[0], TagsMockData[18]];
        preparedTags = TagsHelper.prepareGenericTags(tags);

        expectedTags = _.cloneDeep(tags);
        expectedTags[0].indentationLevel = 0;
        expectedTags[1].parent_id = expectedTags[0].id;
        expectedTags[1].indentationLevel = 1;
      });

      it('shows the tags hierarchy', () => {
        expect(preparedTags).toEqual(expectedTags);
      });
    });

    describe('when displaying tagsets', () => {
      let preparedTags, tags, excpectedTags;

      beforeEach(() => {
        tags = [TagsMockData[5], TagsMockData[6]];
        preparedTags = TagsHelper.prepareTagSetsTree(tags);

        excpectedTags = [_.cloneDeep(tags[0])];
        excpectedTags[0].children = [_.cloneDeep(tags[1])];
      });

      it('shows the tagsets and child tags', () => {
        expect(preparedTags).toEqual(excpectedTags);
      });
    });
  });
})();
