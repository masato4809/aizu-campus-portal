import * as React from 'react';
import { Box } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useIndexContext } from '@/script/Pages/Attendance/Index';
import { Paging } from '@/script/Component/Misc/Paging';
import { MemberTable } from '@/script/Pages/Attendance/Member/TabMember/MemberTable';
import { responsiveSpacing } from '@/script/System/Responsive';

interface IProps extends IPropsBase {}
export const TabMember: React.FC<IProps> = ({ sx }) => {
  const { trnUserList, stateList } = useIndexContext();
  const PAGE_STEP = 50;
  const [currentIndex, setCurrentIndex] = React.useState<number>(0);

  /**
   * 表示するユーザーの状態リスト.
   */
  const userStateList = React.useMemo(() => {
    const userList = trnUserList
      .list()
      .slice(currentIndex * PAGE_STEP, (currentIndex + 1) * PAGE_STEP);
    const userIdList = userList.map(v => v.id);
    return stateList.filter(state => userIdList.includes(state.trnUser.id));
  }, [currentIndex]);

  /**
   * 前のページ.
   */
  const handlePrev = () => {
    setCurrentIndex(prevState => prevState - 1);
  };

  /**
   * 次のページ.
   */
  const handleNext = () => {
    setCurrentIndex(prevState => prevState + 1);
  };

  return (
    <Box
      sx={{
        ...sx,
        display: 'flex',
        flexDirection: 'column',
        gap: responsiveSpacing(4),
        paddingTop: responsiveSpacing(4),
        alignItems: 'end',
      }}
    >
      <Paging
        index={currentIndex}
        step={PAGE_STEP}
        total={trnUserList.list().length}
        handlePrev={handlePrev}
        handleNext={handleNext}
      />
      <MemberTable stateList={userStateList} />
      <Paging
        index={currentIndex}
        step={PAGE_STEP}
        total={trnUserList.list().length}
        handlePrev={handlePrev}
        handleNext={handleNext}
      />
    </Box>
  );
};
