#ifndef _EMDROS_ITERATORS_HPP
#define _EMDROS_ITERATORS_HPP

#include <iterator>
#include <emdros/mql_sheaf.h>


class StrawOk {
    class const_iterator : public std::iterator<std::input_iterator_tag, Straw> {
      public:
        const_iterator(StrawConstIterator se);
        const_iterator(); // Creates an end iterator

        bool operator!=(const const_iterator& rhs);
        const_iterator& operator++();
        const MatchedObject operator*();

      private:
        bool m_end;
        StrawConstIterator m_se;
        const MatchedObject *m_mo;
    };

  public:
    StrawOk(const Straw* stp);
    StrawOk();
    const_iterator begin() const;
    const_iterator end() const;
  private:
    const Straw *m_stp;
};


class SheafOk {
    class const_iterator : public std::iterator<std::input_iterator_tag, StrawOk> {
      public:
        const_iterator(SheafConstIterator se);
        const_iterator(); // Creates an end iterator

        bool operator!=(const const_iterator& rhs);
        const_iterator& operator++();
        const StrawOk operator*();

      private:
        bool m_end;
        SheafConstIterator m_se;
        StrawOk m_str;
    };

  public:
    SheafOk(const Sheaf* shp);
    const_iterator begin() const;
    const_iterator end() const;
  private:
    const Sheaf *m_shp;
};

#endif // _EMDROS_ITERATORS_HPP
