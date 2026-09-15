import * as React from 'react';
import { Autocomplete, Box, TextField } from '@mui/material';
import { IPropsBase } from '@/script/System/System';
import { useFetchAppTrnUserList } from '@/script/Hooks/App/Queries/useFetchAppTrnUserList';
import { Loading } from '@/script/Component/Misc/Loading';
import { Avatar } from '@/script/Pages/Common/Avatar';
import { IAppTrnUser } from '@/script/Models/App/Trn/TrnUserList';

// 表示するオプション
interface IOptionHash {
  [id: number]: string;
}

interface IProps extends IPropsBase {
  value: number[];
  handleChange: (value: number[]) => void;
}
export const SelectTrnUserMultiple: React.FC<IProps> = ({
  sx,
  value,
  handleChange,
}) => {
  const [loadingTrnUser, trnUserList] = useFetchAppTrnUserList();
  const loading = loadingTrnUser;

  // 表示対象.
  const options: number[] = trnUserList.list().map(v => v.id);

  // メモ化した表示情報.
  const optionHashes: IOptionHash = React.useMemo((): IOptionHash => {
    return Object.fromEntries(
      trnUserList.list().map(user => {
        return [user.id, `[${user.id}]${user.nickname}`];
      }),
    );
  }, [options.length]);

  /**
   * アバターのcloseを選択.
   */
  const handleCloseAvatar = (trnUser: IAppTrnUser) => {
    const newValues = value.filter(v => v !== trnUser.id);
    handleChange(newValues);
  };

  return (
    <Box sx={{ ...sx }}>
      <Loading loading={loading}>
        <Autocomplete
          multiple
          value={value}
          options={options}
          getOptionLabel={option => optionHashes[option] as string}
          getOptionKey={option => String(option)}
          onChange={(e, value) => handleChange(value as number[])}
          renderInput={params => (
            <TextField
              {...params}
              sx={{
                width: '100%',
                '& .MuiOutlinedInput-root .MuiAutocomplete-input': {
                  fontSize: '12px',
                  padding: '0px',
                  width: '100%',
                },
              }}
              placeholder="ID、名前で絞り込み"
            />
          )}
          renderTags={(values: number[]) =>
            values.map(v => {
              const trnUser = trnUserList.findByPrimary(v);
              return (
                <Avatar
                  key={trnUser.id}
                  sx={{ margin: '5px' }}
                  trnUser={trnUser}
                  handleClose={handleCloseAvatar}
                />
              );
            })
          }
          disableClearable
        />
      </Loading>
    </Box>
  );
};
