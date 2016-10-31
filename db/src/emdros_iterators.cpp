#include "emdros_iterators.hpp"

StrawOk::const_iterator::const_iterator(StrawConstIterator se)
    : m_se{se}
{
    operator++();
}

StrawOk::const_iterator::const_iterator()
    : m_end{true}
{
}

bool StrawOk::const_iterator::operator!=(const const_iterator& rhs)
{
    return m_end!=rhs.m_end;
}

StrawOk::const_iterator& StrawOk::const_iterator::operator++()
{
    if (m_se.hasNext()) {
        m_end = false;
        m_mo = m_se.next();
    }
    else
        m_end = true;
}


const MatchedObject StrawOk::const_iterator::operator*()
{
    return *m_mo;
}


StrawOk::StrawOk(const Straw* stp)
    : m_stp{stp}
{
}

StrawOk::StrawOk()
    : m_stp{0}
{
}

StrawOk::const_iterator StrawOk::begin() const
{
    return const_iterator{m_stp->const_iterator()};
}

StrawOk::const_iterator StrawOk::end() const
{
    return const_iterator{};
}


SheafOk::const_iterator::const_iterator(SheafConstIterator se)
    : m_se{se}
{
    operator++();
}

SheafOk::const_iterator::const_iterator()
    : m_end{true}
{
}

bool SheafOk::const_iterator::operator!=(const const_iterator& rhs)
{
    return m_end!=rhs.m_end;
}

SheafOk::const_iterator& SheafOk::const_iterator::operator++()
{
    if (m_se.hasNext()) {
        m_end = false;
        m_str = StrawOk{m_se.next()};
    }
    else
        m_end = true;
}

const StrawOk SheafOk::const_iterator::operator*()
{
    return m_str;
}


SheafOk::SheafOk(const Sheaf* shp)
    : m_shp{shp}
{
}

SheafOk::const_iterator SheafOk::begin() const
{
    return const_iterator{m_shp->const_iterator()};
}

SheafOk::const_iterator SheafOk::end() const
{
    return const_iterator{};
}
